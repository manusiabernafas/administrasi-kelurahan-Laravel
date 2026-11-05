<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\WargaController;
use App\Models\Layanan;
use App\Models\Penduduk;
use App\Models\Permohonan;
use App\Support\Surat;
use Illuminate\Http\Request;

class PermohonanController extends Controller
{
    /** Status berikutnya yang boleh dipilih dari status sekarang. */
    public const ALUR = [
        'diajukan' => ['diverifikasi', 'ditolak'],
        'diverifikasi' => ['diproses', 'selesai', 'ditolak'],
        'diproses' => ['selesai', 'ditolak'],
        'selesai' => [],
        'ditolak' => [],
    ];

    public function index(Request $r)
    {
        $q = Permohonan::with('layanan', 'penduduk')->latest();
        if ($s = $r->query('status')) {
            $s === 'aktif' ? $q->whereIn('status', ['diajukan', 'diverifikasi', 'diproses']) : $q->where('status', $s);
        }
        if ($l = $r->query('layanan')) {
            $q->where('layanan_id', $l);
        }
        if ($c = trim((string) $r->query('q'))) {
            $q->where(fn ($w) => $w->where('kode', 'like', "%{$c}%")->orWhere('nomor_surat', 'like', "%{$c}%")->orWhereHas('penduduk', fn ($p) => $p->where('nama', 'like', "%{$c}%")->orWhere('nik', 'like', "%{$c}%")));
        }
        return view('admin.permohonan.index', [
            'daftar' => $q->paginate(15)->withQueryString(), 'layanan' => Layanan::orderBy('urutan')->get(),
            'hitung' => Permohonan::selectRaw('status, COUNT(*) n')->groupBy('status')->pluck('n', 'status'),
        ]);
    }

    /** Layanan loket: petugas mengisi permohonan untuk warga yang datang langsung. */
    public function create(Request $r)
    {
        return view('admin.permohonan.create', [
            'layanan' => Layanan::where('aktif', true)->orderBy('urutan')->get(),
            'pilih' => Layanan::find($r->query('layanan')),
            'penduduk' => $r->query('nik') ? Penduduk::where('nik', $r->query('nik'))->first() : null,
        ]);
    }

    public function cariPenduduk(Request $r)
    {
        $q = trim((string) $r->query('q'));
        if (mb_strlen($q) < 3) {
            return response()->json([]);
        }
        return response()->json(Penduduk::where('status', 'aktif')->where(fn ($w) => $w->where('nik', 'like', "{$q}%")->orWhere('nama', 'like', "%{$q}%"))->orderBy('nama')->limit(8)->get(['id', 'nik', 'nama', 'alamat', 'rt', 'rw']));
    }

    public function store(Request $r)
    {
        $l = Layanan::where('aktif', true)->findOrFail($r->input('layanan_id'));
        [$aturan, $label] = WargaController::aturanIsian($l);
        $d = $r->validate($aturan + ['penduduk_id' => 'required|exists:penduduk,id', 'keperluan' => 'required|string|min:5|max:255', 'langsung_selesai' => 'nullable|boolean'], [], $label + ['penduduk_id' => 'penduduk']);
        $p = Permohonan::create([
            'kode' => WargaController::kodeLacak(), 'layanan_id' => $l->id, 'penduduk_id' => $d['penduduk_id'], 'petugas_id' => $r->user()->id,
            'keperluan' => $d['keperluan'], 'data' => $d['data'] ?? [], 'lampiran' => [], 'status' => 'diverifikasi', 'saluran' => 'loket',
        ]);
        $p->catat('diajukan', 'Diajukan di loket pelayanan.', $r->user()->id);
        $p->catat('diverifikasi', 'Berkas diperiksa langsung oleh petugas.', $r->user()->id);
        if ($r->boolean('langsung_selesai')) {
            $this->selesaikan($p, $r->user()->id, 'Surat langsung diterbitkan di loket.');
        }
        return redirect()->route('admin.permohonan.show', $p)->with('ok', 'Permohonan ' . $p->kode . ' dibuat.');
    }

    public function show(Permohonan $permohonan)
    {
        $permohonan->load('layanan', 'penduduk', 'riwayat.user', 'pemohon', 'petugas');
        return view('admin.permohonan.show', ['p' => $permohonan, 'alur' => self::ALUR[$permohonan->status] ?? []]);
    }

    public function status(Request $r, Permohonan $permohonan)
    {
        $boleh = self::ALUR[$permohonan->status] ?? [];
        $d = $r->validate(['status' => 'required|in:' . implode(',', $boleh ?: ['-']), 'catatan' => 'nullable|string|max:500|required_if:status,ditolak'], ['catatan.required_if' => 'Tulis alasan penolakan agar warga tahu yang harus diperbaiki.']);
        if ($d['status'] === 'selesai') {
            $this->selesaikan($permohonan, $r->user()->id, $d['catatan'] ?? null);
        } else {
            $permohonan->update(['status' => $d['status'], 'petugas_id' => $r->user()->id, 'catatan' => $d['catatan'] ?? $permohonan->catatan]);
            $permohonan->catat($d['status'], $d['catatan'] ?? null, $r->user()->id);
        }
        return back()->with('ok', 'Status diubah menjadi ' . Permohonan::STATUS[$d['status']][0] . '.');
    }

    private function selesaikan(Permohonan $p, int $uid, ?string $catatan): void
    {
        $p->load('layanan');
        Surat::terbitkan($p);
        $p->update(['status' => 'selesai', 'selesai_at' => now(), 'petugas_id' => $uid]);
        $p->catat('selesai', $catatan ?: 'Surat terbit dengan nomor ' . $p->nomor_surat . '.', $uid);
    }

    /** Pratinjau surat (PDF di browser). Sebelum selesai, nomor surat masih kosong. */
    public function pratinjau(Permohonan $permohonan)
    {
        return Surat::pdf($permohonan)->stream('pratinjau-' . $permohonan->kode . '.pdf');
    }
}
