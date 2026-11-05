<?php

namespace App\Support;

use Illuminate\Support\Facades\DB;

/** Pengaturan identitas kelurahan (tabel pengaturan, kunci-nilai). */
class Kelurahan
{
    public const DEFAULT = [
        'nama' => 'Sukamaju',
        'kecamatan' => 'Cendana',
        'kota' => 'Kota Harapan',
        'provinsi' => 'Jawa Barat',
        'kode_pos' => '40111',
        'alamat' => 'Jl. Merdeka No. 12',
        'telepon' => '(022) 000-1234',
        'whatsapp' => '081200001234',
        'email' => 'kelurahan@contoh.go.id',
        'jam_layanan' => "Senin - Kamis: 08.00 - 15.00\nJumat: 08.00 - 11.30\nSabtu & Minggu: tutup",
        'nama_lurah' => 'Drs. Nama Lurah, M.Si.',
        'nip_lurah' => '19700101 199503 1 001',
        'kode_surat' => 'KL-SKM',
        'logo' => '',
        'peta' => '',
        'tagline' => 'Urus surat tanpa antre, cukup dari rumah',
        'sambutan' => 'Kami berkomitmen memberikan pelayanan administrasi yang cepat, transparan, dan tanpa pungutan liar. Ajukan permohonan surat secara online, pantau prosesnya, lalu ambil atau unduh suratnya begitu selesai.',
    ];

    private static ?array $cache = null;

    public static function semua(): array
    {
        return self::$cache ??= array_merge(self::DEFAULT, DB::table('pengaturan')->pluck('nilai', 'kunci')->map(fn ($v) => (string) $v)->all());
    }

    public static function get(string $k): string
    {
        return self::semua()[$k] ?? '';
    }

    public static function simpan(array $data): void
    {
        foreach ($data as $k => $v) {
            DB::table('pengaturan')->updateOrInsert(['kunci' => $k], ['nilai' => (string) $v]);
        }
        self::$cache = null;
    }

    public static function namaLengkap(): string
    {
        return 'Kelurahan ' . self::get('nama');
    }

    public static function waLink(string $pesan = ''): string
    {
        $no = preg_replace('/\D/', '', self::get('whatsapp'));
        if ($no === '') {
            return '';
        }
        $no = str_starts_with($no, '0') ? '62' . substr($no, 1) : $no;
        return 'https://wa.me/' . $no . ($pesan !== '' ? '?text=' . rawurlencode($pesan) : '');
    }
}
