<?php

namespace App\Http\Controllers;

use App\Models\Layanan;
use App\Models\Permohonan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password;

class WargaController extends Controller
{
    public function beranda(Request $r)
    {
        $u = $r->user();
        return view('warga.beranda', [
            'penduduk' => $u->penduduk,
            'daftar' => Permohonan::with('layanan')->where('user_id', $u->id)->latest()->get(),
            'layanan' => Layanan::where('aktif', true)->orderBy('urutan')->limit(6)->get(),
        ]);
    }

    public function pilih()
    {
        return view('warga.pilih', ['layanan' => Layanan::where('aktif', true)->orderBy('urutan')->get()]);
    }

    public function ajukan(Request $r, string $kode)
    {
        $l = Layanan::where(['kode' => strtoupper($kode), 'aktif' => true])->firstOrFail();
        return view('warga.ajukan', ['l' => $l, 'penduduk' => $r->user()->penduduk]);
    }

    /** Aturan validasi isian tambahan (dipakai juga oleh petugas di loket). */
    public static function aturanIsian(Layanan $l): array
    {
        $aturan = [];
        $label = [];
        foreach ($l->isian as $f) {
            $aturan['data.' . $f['nama']] = array_filter([$f['wajib'] ? 'required' : 'nullable', match ($f['tipe']) { 'date' => 'date', 'number' => 'numeric', default => 'string' }, $f['tipe'] === 'textarea' ? 'max:1000' : ($f['tipe'] === 'text' ? 'max:200' : null)]);
            $label['data.' . $f['nama']] = strtolower($f['label']);
        }
        return [$aturan, $label];
    }

    public static function kodeLacak(): string
    {
        do {
            $kode = 'KLR-' . now()->format('ym') . '-' . strtoupper(Str::random(5));
        } while (Permohonan::where('kode', $kode)->exists());
        return $kode;
    }

    public function simpan(Request $r, string $kode)
    {
        $l = Layanan::where(['kode' => strtoupper($kode), 'aktif' => true])->firstOrFail();
        $penduduk = $r->user()->penduduk;
        abort_unless($penduduk && $penduduk->status === 'aktif', 403, 'Data penduduk Anda tidak aktif. Hubungi kelurahan.');
        [$aturan, $label] = self::aturanIsian($l);
        $d = $r->validate($aturan + [
            'keperluan' => 'required|string|min:5|max:255',
            'lampiran' => 'nullable|array|max:5', 'lampiran.*' => 'file|mimes:jpg,jpeg,png,pdf|max:3072',
            'setuju' => 'accepted',
        ], ['setuju.accepted' => 'Centang pernyataan bahwa data yang diisi benar.'], $label + ['lampiran.*' => 'lampiran']);
        $berjalan = Permohonan::where(['penduduk_id' => $penduduk->id, 'layanan_id' => $l->id])->whereIn('status', ['diajukan', 'diverifikasi', 'diproses'])->exists();
        if ($berjalan) {
            return back()->withInput()->with('error', 'Anda masih punya permohonan ' . $l->nama . ' yang sedang diproses. Tunggu sampai selesai.');
        }
        $lampiran = [];
        foreach ($r->file('lampiran', []) as $f) {
            $lampiran[] = ['nama' => Str::limit($f->getClientOriginalName(), 80, ''), 'path' => $f->store('lampiran', 'local')];
        }
        $p = Permohonan::create([
            'kode' => self::kodeLacak(), 'layanan_id' => $l->id, 'penduduk_id' => $penduduk->id, 'user_id' => $r->user()->id,
            'keperluan' => $d['keperluan'], 'data' => $d['data'] ?? [], 'lampiran' => $lampiran, 'status' => 'diajukan', 'saluran' => 'online',
        ]);
        $p->catat('diajukan', 'Permohonan diajukan secara online.', $r->user()->id);
        return redirect()->route('warga.detail', $p->id)->with('ok', 'Permohonan terkirim. Simpan kode lacak ' . $p->kode . ' untuk cek status.');
    }

    public function detail(Request $r, int $id)
    {
        $p = Permohonan::with('layanan', 'riwayat', 'penduduk')->where('user_id', $r->user()->id)->findOrFail($id);
        return view('warga.detail', ['p' => $p]);
    }

    public function batal(Request $r, int $id)
    {
        $p = Permohonan::where('user_id', $r->user()->id)->findOrFail($id);
        if ($p->status !== 'diajukan') {
            return back()->with('error', 'Permohonan yang sudah diperiksa petugas tidak bisa dibatalkan.');
        }
        $p->update(['status' => 'ditolak', 'catatan' => 'Dibatalkan oleh pemohon.']);
        $p->catat('ditolak', 'Dibatalkan oleh pemohon.', $r->user()->id);
        return back()->with('ok', 'Permohonan dibatalkan.');
    }

    public function profil(Request $r)
    {
        return view('warga.profil', ['u' => $r->user(), 'penduduk' => $r->user()->penduduk]);
    }

    public function simpanProfil(Request $r)
    {
        $d = $r->validate(['telepon' => ['required', 'regex:/^(\+62|62|0)8[0-9]{7,12}$/'], 'email' => 'nullable|email|max:150|unique:users,email,' . $r->user()->id], ['telepon.regex' => 'Nomor HP tidak valid.'], ['telepon' => 'nomor HP']);
        $r->user()->update($d);
        return back()->with('ok', 'Profil disimpan.');
    }

    public function sandi(Request $r)
    {
        $r->validate(['sandi_lama' => 'required|current_password', 'password' => ['required', 'confirmed', Password::min(8)->letters()->numbers()]], [], ['sandi_lama' => 'kata sandi lama', 'password' => 'kata sandi baru']);
        $r->user()->update(['password' => Hash::make($r->input('password'))]);
        return back()->with('ok', 'Kata sandi diganti.');
    }
}
