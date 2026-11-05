<?php

namespace App\Http\Controllers;

use App\Models\Layanan;
use App\Models\Pengaduan;
use App\Models\Pengumuman;
use App\Models\Permohonan;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PublikController extends Controller
{
    public function beranda()
    {
        return view('publik.beranda', [
            'layanan' => Layanan::where('aktif', true)->orderBy('urutan')->get(),
            'kabar' => Pengumuman::tampil()->latest('terbit_at')->limit(3)->get(),
            'angka' => [
                'selesai' => Permohonan::where('status', 'selesai')->count(),
                'bulan' => Permohonan::where('created_at', '>=', now()->startOfMonth())->count(),
                'rata' => $this->rataHari(),
                'puas' => 98,
            ],
        ]);
    }

    /** Rata-rata lama proses (hari) permohonan yang selesai 90 hari terakhir. */
    private function rataHari(): float
    {
        $rows = Permohonan::where('status', 'selesai')->where('selesai_at', '>=', now()->subDays(90))->get(['created_at', 'selesai_at']);
        if ($rows->isEmpty()) {
            return 1;
        }
        return round($rows->avg(fn ($p) => max(0.1, $p->created_at->diffInHours($p->selesai_at) / 24)), 1);
    }

    public function layanan()
    {
        return view('publik.layanan', ['layanan' => Layanan::where('aktif', true)->orderBy('urutan')->get()]);
    }

    public function layananDetail(string $kode)
    {
        $l = Layanan::where(['kode' => strtoupper($kode), 'aktif' => true])->firstOrFail();
        return view('publik.layanan-detail', ['l' => $l, 'lain' => Layanan::where('aktif', true)->where('id', '!=', $l->id)->orderBy('urutan')->limit(4)->get()]);
    }

    /** Cek status permohonan / pengaduan memakai kode lacak (tanpa login). */
    public function cekStatus(Request $r)
    {
        $kode = strtoupper(trim((string) $r->query('kode')));
        $p = $a = null;
        if ($kode !== '') {
            $p = Permohonan::with('layanan', 'riwayat', 'penduduk')->where('kode', $kode)->first();
            $a = $p ? null : Pengaduan::where('kode', $kode)->first();
        }
        return view('publik.cek-status', ['kode' => $kode, 'p' => $p, 'a' => $a]);
    }

    /** Halaman tujuan QR di surat: memastikan surat asli diterbitkan kelurahan. */
    public function verifikasi(string $kode)
    {
        $p = Permohonan::with('layanan', 'penduduk')->where('kode_verifikasi', $kode)->where('status', 'selesai')->first();
        return response()->view('publik.verifikasi', ['p' => $p], $p ? 200 : 404);
    }

    public function kabar(Request $r)
    {
        $kat = $r->query('kategori');
        return view('publik.kabar', [
            'daftar' => Pengumuman::tampil()->when($kat, fn ($q) => $q->where('kategori', $kat))->latest('terbit_at')->paginate(9)->withQueryString(),
            'kategori' => Pengumuman::tampil()->distinct()->orderBy('kategori')->pluck('kategori'), 'aktif' => $kat,
        ]);
    }

    public function kabarDetail(string $slug)
    {
        $k = Pengumuman::tampil()->where('slug', $slug)->firstOrFail();
        $k->increment('dilihat');
        return view('publik.kabar-detail', ['k' => $k, 'lain' => Pengumuman::tampil()->where('id', '!=', $k->id)->latest('terbit_at')->limit(3)->get()]);
    }

    public function pengaduan()
    {
        return view('publik.pengaduan');
    }

    public function kirimPengaduan(Request $r)
    {
        if ($r->filled('situs')) { // honeypot
            return redirect()->route('pengaduan')->with('ok', 'Terima kasih, pengaduan terkirim.');
        }
        $d = $r->validate([
            'nama' => 'required|string|min:3|max:120', 'kontak' => 'required|string|min:6|max:120',
            'kategori' => 'required|in:' . implode(',', Pengaduan::KATEGORI), 'lokasi' => 'nullable|string|max:200',
            'isi' => 'required|string|min:20|max:3000', 'lampiran' => 'nullable|file|mimes:jpg,jpeg,png,webp|max:3072',
        ], [], ['isi' => 'isi pengaduan', 'kontak' => 'nomor HP / email']);
        if ($r->hasFile('lampiran')) {
            $d['lampiran'] = $r->file('lampiran')->store('pengaduan', 'local');
        }
        $d['kode'] = 'ADU-' . strtoupper(Str::random(6));
        $d['user_id'] = $r->user()?->id;
        Pengaduan::create($d);
        return redirect()->route('pengaduan')->with('kode', $d['kode']);
    }
}
