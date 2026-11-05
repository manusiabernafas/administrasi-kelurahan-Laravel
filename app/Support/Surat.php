<?php

namespace App\Support;

use App\Models\Permohonan;
use Barryvdh\DomPDF\Facade\Pdf;
use chillerlan\QRCode\QRCode;
use chillerlan\QRCode\QROptions;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/** Penomoran & pembuatan surat keterangan (PDF dengan QR verifikasi). */
class Surat
{
    public static function romawi(int $n): string
    {
        return ['', 'I', 'II', 'III', 'IV', 'V', 'VI', 'VII', 'VIII', 'IX', 'X', 'XI', 'XII'][$n] ?? (string) $n;
    }

    /** Terbitkan nomor surat (urut per tahun, aman dari nomor ganda). */
    public static function terbitkan(Permohonan $p): void
    {
        DB::transaction(function () use ($p) {
            $tahun = (int) now()->format('Y');
            $urut = (int) DB::table('permohonan')->whereYear('tanggal_surat', $tahun)->whereNotNull('nomor_surat')->lockForUpdate()->count() + 1;
            do {
                $nomor = sprintf('%03d/%s/%s/%s/%d', $urut, $p->layanan->kode, Kelurahan::get('kode_surat'), self::romawi((int) now()->format('n')), $tahun);
                $urut++;
            } while (DB::table('permohonan')->where('nomor_surat', $nomor)->exists());
            $p->forceFill(['nomor_surat' => $nomor, 'tanggal_surat' => now()->toDateString(), 'kode_verifikasi' => $p->kode_verifikasi ?: Str::lower(Str::random(24))])->save();
        });
    }

    /** Ganti {placeholder} di isi surat dengan data penduduk & isian permohonan. */
    public static function isi(Permohonan $p): string
    {
        $d = $p->penduduk;
        $k = Kelurahan::semua();
        $nilai = [
            'nama' => $d->nama, 'nik' => $d->nik, 'no_kk' => $d->no_kk, 'ttl' => $d->ttl, 'jenis_kelamin' => $d->jenis_kelamin === 'L' ? 'Laki-laki' : 'Perempuan',
            'agama' => $d->agama, 'pekerjaan' => $d->pekerjaan, 'status_perkawinan' => $d->status_perkawinan, 'alamat' => $d->alamat_lengkap, 'rt' => $d->rt, 'rw' => $d->rw,
            'keperluan' => $p->keperluan, 'kelurahan' => $k['nama'], 'kecamatan' => $k['kecamatan'], 'kota' => $k['kota'], 'tanggal' => tgl($p->tanggal_surat ?? now()),
        ];
        foreach ((array) $p->data as $kunci => $v) {
            $nilai[$kunci] = is_string($v) && preg_match('/^\d{4}-\d{2}-\d{2}$/', $v) ? tgl($v) : (string) $v;
        }
        // teks surat di-escape dulu, baru placeholder diganti nilai yang juga di-escape (hasilnya aman dicetak sebagai HTML)
        return preg_replace_callback('/\{([a-z_]+)\}/', fn ($m) => e($nilai[$m[1]] ?? '-'), e($p->layanan->isi_surat));
    }

    public static function qr(string $teks): string
    {
        $opt = new QROptions(['outputType' => QRCode::OUTPUT_MARKUP_SVG, 'outputBase64' => true, 'eccLevel' => QRCode::ECC_M, 'addQuietzone' => true, 'svgUseFillAttributes' => true]);
        return (new QRCode($opt))->render($teks);
    }

    public static function pdf(Permohonan $p)
    {
        $p->loadMissing('layanan', 'penduduk');
        $logo = Kelurahan::get('logo');
        $logoPath = $logo && is_file(public_path('uploads/' . $logo)) ? public_path('uploads/' . $logo) : public_path('img/logo-surat.svg');
        $logoData = 'data:' . (str_ends_with($logoPath, '.svg') ? 'image/svg+xml' : mime_content_type($logoPath)) . ';base64,' . base64_encode(file_get_contents($logoPath));
        return Pdf::loadView('surat.pdf', [
            'p' => $p, 'k' => Kelurahan::semua(), 'isi' => self::isi($p), 'logo' => $logoData,
            'qr' => $p->kode_verifikasi ? self::qr(route('verifikasi', $p->kode_verifikasi)) : null,
        ])->setPaper('a4');
    }
}
