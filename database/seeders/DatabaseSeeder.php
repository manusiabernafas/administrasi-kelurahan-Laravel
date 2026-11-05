<?php

namespace Database\Seeders;

use App\Models\Layanan;
use App\Models\Pengaduan;
use App\Models\Penduduk;
use App\Models\Pengumuman;
use App\Models\Permohonan;
use App\Models\User;
use App\Support\Surat;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

/**
 * Data contoh Kelurahan Sukamaju (fiktif).
 * Akun: admin@kelurahan.test / admin123 (admin), petugas@kelurahan.test / petugas123 (petugas),
 *       warga NIK 3273010101900001 / warga123
 */
class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        mt_srand(2026);
        $admin = User::create(['name' => 'Rina Kartika', 'email' => 'admin@kelurahan.test', 'peran' => 'admin', 'password' => 'admin123', 'aktif' => true, 'telepon' => '081200000001']);
        $petugas = User::create(['name' => 'Dedi Kurniawan', 'email' => 'petugas@kelurahan.test', 'peran' => 'petugas', 'password' => 'petugas123', 'aktif' => true, 'telepon' => '081200000002']);

        foreach ($this->layanan() as $i => $l) {
            Layanan::create($l + ['urutan' => $i + 1, 'aktif' => true]);
        }

        // penduduk: 45 keluarga
        $depanL = ['Budi', 'Agus', 'Dedi', 'Hendra', 'Rudi', 'Joko', 'Andi', 'Bayu', 'Fajar', 'Rizky', 'Dimas', 'Arif', 'Yusuf', 'Taufik', 'Wahyu', 'Eko', 'Imam', 'Ilham', 'Gilang', 'Hadi'];
        $depanP = ['Siti', 'Dewi', 'Rina', 'Lestari', 'Ayu', 'Nur', 'Fitri', 'Wulan', 'Indah', 'Ratna', 'Sari', 'Putri', 'Anisa', 'Yuni', 'Maya', 'Lina', 'Nadia', 'Tika', 'Rahma', 'Desi'];
        $belakang = ['Santoso', 'Saputra', 'Hidayat', 'Wijaya', 'Pratama', 'Kurniawan', 'Setiawan', 'Rahman', 'Gunawan', 'Nugroho', 'Permana', 'Lestari', 'Maharani', 'Rahayu', 'Purnama', 'Hakim', 'Firmansyah', 'Ramadhan'];
        $kerja = ['Karyawan Swasta', 'Wiraswasta', 'Pedagang', 'PNS', 'Guru', 'Buruh Harian Lepas', 'Petani', 'Sopir', 'Perawat', 'Pelajar/Mahasiswa', 'Mengurus Rumah Tangga', 'Pensiunan'];
        $jalan = ['Jl. Melati', 'Jl. Kenanga', 'Jl. Flamboyan', 'Gg. Mawar', 'Jl. Cempaka', 'Gg. Anggrek', 'Jl. Dahlia', 'Jl. Teratai'];
        $semua = [];
        for ($kk = 0; $kk < 45; $kk++) {
            $noKk = '32730101' . sprintf('%02d', 10 + $kk % 80) . sprintf('%06d', 100000 + $kk * 37);
            $rw = sprintf('%03d', 1 + $kk % 5);
            $rt = sprintf('%03d', 1 + $kk % 4);
            $alamat = $jalan[$kk % count($jalan)] . ' No. ' . (3 + $kk * 2);
            $marga = $belakang[$kk % count($belakang)];
            $anggota = [['L', 1975 + $kk % 20, 'Kawin'], ['P', 1978 + $kk % 18, 'Kawin']];
            for ($a = 0, $n = mt_rand(0, 3); $a < $n; $a++) {
                $anggota[] = [mt_rand(0, 1) ? 'L' : 'P', 2000 + mt_rand(0, 20), 'Belum Kawin'];
            }
            foreach ($anggota as $j => [$jk, $th, $kawin]) {
                $tgl = Carbon::create($th, 1 + ($kk + $j) % 12, 1 + ($kk * 3 + $j * 7) % 28);
                $nikTgl = sprintf('%02d%02d%02d', $tgl->day + ($jk === 'P' ? 40 : 0), $tgl->month, $tgl->year % 100);
                $nik = $kk === 0 && $j === 0 ? '3273010101900001' : '327301' . $nikTgl . sprintf('%04d', 1 + $kk * 10 + $j);
                $nama = ($jk === 'L' ? $depanL[($kk + $j * 3) % 20] : $depanP[($kk + $j * 5) % 20]) . ' ' . $marga;
                if ($kk === 0 && $j === 0) {
                    [$nama, $tgl] = ['Budi Santoso', Carbon::create(1990, 1, 1)];
                }
                $umur = $tgl->age;
                $semua[] = Penduduk::create([
                    'nik' => $nik, 'no_kk' => $noKk, 'nama' => mb_strtoupper($nama), 'tempat_lahir' => ['Bandung', 'Garut', 'Cimahi', 'Sumedang', 'Tasikmalaya', 'Jakarta'][($kk + $j) % 6],
                    'tanggal_lahir' => $tgl, 'jenis_kelamin' => $jk, 'agama' => $kk % 9 === 4 ? 'Kristen' : ($kk % 13 === 6 ? 'Katolik' : 'Islam'),
                    'status_perkawinan' => $kawin, 'pekerjaan' => $umur < 23 ? 'Pelajar/Mahasiswa' : ($jk === 'P' && $j === 1 && $kk % 3 === 0 ? 'Mengurus Rumah Tangga' : $kerja[($kk + $j) % count($kerja)]),
                    'pendidikan' => $umur < 15 ? 'SD' : ['SMA/SMK', 'S1', 'SMP', 'D3', 'SMA/SMK', 'S2'][($kk + $j) % 6], 'alamat' => $alamat, 'rt' => $rt, 'rw' => $rw,
                    'kewarganegaraan' => 'WNI', 'status' => $kk === 44 && $j === 1 ? 'pindah' : 'aktif',
                ]);
            }
        }
        $budi = $semua[0];
        $warga = User::create(['name' => 'BUDI SANTOSO', 'nik' => $budi->nik, 'peran' => 'warga', 'password' => 'warga123', 'aktif' => true, 'telepon' => '081234567890']);
        $wargaLain = collect(array_slice($semua, 2, 30))->filter(fn ($p) => $p->tanggal_lahir->age >= 17)->take(10)->map(fn ($p) => User::create(['name' => $p->nama, 'nik' => $p->nik, 'peran' => 'warga', 'password' => Str::random(16), 'aktif' => true, 'telepon' => '0812' . mt_rand(10000000, 99999999)]));

        // permohonan 60 hari terakhir
        $layanan = Layanan::all()->keyBy('kode');
        $keperluan = ['SKD' => 'Melamar pekerjaan', 'SKTM' => 'Pengajuan beasiswa KIP Kuliah', 'SKCK' => 'Persyaratan melamar kerja', 'SKU' => 'Pengajuan KUR di bank', 'SKBM' => 'Persyaratan pendaftaran nikah di KUA', 'SKL' => 'Pengurusan akta kelahiran', 'SKM' => 'Pengurusan akta kematian dan klaim asuransi', 'SKP' => 'Pindah domisili karena pekerjaan'];
        $isian = [
            'SKU' => fn () => ['nama_usaha' => ['Warung Sembako Berkah', 'Laundry Bersih Wangi', 'Bengkel Motor Jaya', 'Katering Bu Sari', 'Toko Kue Manis'][mt_rand(0, 4)], 'jenis_usaha' => ['Perdagangan', 'Jasa', 'Kuliner'][mt_rand(0, 2)], 'alamat_usaha' => 'Jl. Melati No. ' . mt_rand(1, 60), 'mulai_usaha' => (string) mt_rand(2015, 2025)],
            'SKL' => fn () => ['nama_bayi' => ['Aisyah Putri', 'Muhammad Rafa', 'Nadia Azzahra', 'Raka Pratama'][mt_rand(0, 3)], 'tanggal_lahir_bayi' => now()->subDays(mt_rand(5, 40))->toDateString(), 'tempat_lahir_bayi' => 'RSUD Kota Harapan', 'nama_ibu' => 'SITI RAHAYU', 'jenis_kelamin_bayi' => 'Perempuan'],
            'SKM' => fn () => ['nama_almarhum' => 'H. SOLEH HIDAYAT', 'tanggal_meninggal' => now()->subDays(mt_rand(3, 30))->toDateString(), 'tempat_meninggal' => 'Rumah duka', 'sebab' => 'Sakit', 'hubungan' => 'Anak kandung'],
            'SKP' => fn () => ['alamat_tujuan' => 'Jl. Pahlawan No. 21, Kota Bandung', 'alasan_pindah' => 'Pekerjaan', 'jumlah_pengikut' => (string) mt_rand(0, 3)],
            'SKTM' => fn () => ['penghasilan' => (string) (mt_rand(8, 25) * 100000), 'tanggungan' => (string) mt_rand(2, 5)],
        ];
        $pemohon = collect($semua)->filter(fn ($p) => $p->status === 'aktif' && $p->tanggal_lahir->age >= 17)->values();
        $kodeLayanan = ['SKD', 'SKD', 'SKD', 'SKTM', 'SKTM', 'SKCK', 'SKCK', 'SKCK', 'SKU', 'SKU', 'SKBM', 'SKL', 'SKM', 'SKP'];
        for ($i = 0; $i < 64; $i++) {
            $kode = $kodeLayanan[$i % count($kodeLayanan)];
            $l = $layanan[$kode];
            $p = $i < 3 ? $budi : $pemohon[($i * 7) % $pemohon->count()];
            $u = $p->id === $budi->id ? $warga : $wargaLain->firstWhere('nik', $p->nik);
            $masuk = now()->subDays(max(0, 60 - $i))->setTime(8 + $i % 7, ($i * 13) % 60);
            $status = $i >= 58 ? 'diajukan' : ($i >= 55 ? 'diverifikasi' : ($i >= 52 ? 'diproses' : ($i % 11 === 5 ? 'ditolak' : 'selesai')));
            if ($i === 0) [$status, $kode] = ['selesai', 'SKD'];
            if ($i === 1) $status = 'diproses';
            $saluran = $u ? 'online' : 'loket';
            $pm = Permohonan::create([
                'kode' => 'KLR-' . $masuk->format('ym') . '-' . strtoupper(Str::random(5)), 'layanan_id' => $l->id, 'penduduk_id' => $p->id, 'user_id' => $u?->id,
                'petugas_id' => $status === 'diajukan' ? null : $petugas->id, 'keperluan' => $keperluan[$kode], 'data' => isset($isian[$kode]) ? $isian[$kode]() : [],
                'lampiran' => [], 'status' => 'diajukan', 'saluran' => $saluran, 'created_at' => $masuk, 'updated_at' => $masuk,
            ]);
            $pm->riwayat()->create(['status' => 'diajukan', 'catatan' => $saluran === 'online' ? 'Permohonan diajukan secara online.' : 'Diajukan di loket pelayanan.', 'user_id' => $u?->id, 'created_at' => $masuk]);
            $t = $masuk->copy();
            foreach (['diverifikasi', 'diproses', 'selesai'] as $s) {
                if ($status === 'diajukan' || ($status === 'ditolak')) break;
                $t = $t->copy()->addHours(mt_rand(1, 3));
                if ($t->hour >= 15) $t = $t->copy()->addDay()->setTime(8 + mt_rand(0, 2), mt_rand(0, 59));
                if ($s === 'selesai' && $status !== 'selesai') break;
                if ($s === 'selesai') {
                    Carbon::setTestNow($t);
                    Surat::terbitkan($pm->fresh('layanan'));
                    Carbon::setTestNow();
                    $pm->update(['status' => 'selesai', 'selesai_at' => $t]);
                    $pm->riwayat()->create(['status' => 'selesai', 'catatan' => 'Surat terbit dengan nomor ' . $pm->fresh()->nomor_surat . '.', 'user_id' => $petugas->id, 'created_at' => $t]);
                    break;
                }
                $pm->update(['status' => $s]);
                $pm->riwayat()->create(['status' => $s, 'catatan' => $s === 'diverifikasi' ? 'Berkas lengkap.' : null, 'user_id' => $petugas->id, 'created_at' => $t]);
                if ($s === $status) break;
            }
            if ($status === 'ditolak') {
                $t = $masuk->copy()->addHours(5);
                $pm->update(['status' => 'ditolak', 'catatan' => 'Foto KK tidak terbaca dan surat pengantar RT belum dilampirkan. Silakan ajukan ulang.']);
                $pm->riwayat()->create(['status' => 'ditolak', 'catatan' => 'Foto KK tidak terbaca dan surat pengantar RT belum dilampirkan. Silakan ajukan ulang.', 'user_id' => $petugas->id, 'created_at' => $t]);
            }
        }

        @mkdir(public_path('uploads/kabar'), 0775, true);
        foreach ($this->kabar() as $i => [$judul, $kat, $isi]) {
            $gbr = 'kabar/contoh-' . ($i + 1) . '.jpg';
            @copy(__DIR__ . '/aset/kabar-' . ($i + 1) . '.jpg', public_path('uploads/' . $gbr));
            Pengumuman::create(['judul' => $judul, 'slug' => Str::slug($judul), 'kategori' => $kat, 'ringkas' => Str::limit($isi, 160), 'isi' => $isi, 'gambar' => $gbr, 'terbit' => true, 'terbit_at' => now()->subDays(2 + $i * 6)->setTime(9, 0), 'dilihat' => mt_rand(40, 400)]);
        }

        $aduan = [
            ['Infrastruktur', 'Jalan berlubang di Jl. Kenanga dekat masjid sudah sebulan belum diperbaiki. Saat hujan tergenang dan beberapa pengendara motor terjatuh.', 'Jl. Kenanga depan Masjid Al-Ikhlas', 'selesai', 'Terima kasih laporannya. Lubang jalan sudah ditambal oleh Dinas PUPR pada 12 Oktober. Mohon kabari jika masih ada kerusakan.'],
            ['Kebersihan', 'Tumpukan sampah di TPS RW 03 tidak diangkut sejak hari Senin dan mulai berbau.', 'TPS RW 03', 'ditanggapi', 'Sudah kami koordinasikan dengan petugas kebersihan kecamatan. Pengangkutan dijadwalkan besok pagi.'],
            ['Infrastruktur', 'Lampu penerangan jalan di Gg. Mawar mati, jalan jadi gelap dan rawan.', 'Gg. Mawar RT 002/RW 001', 'baru', null],
            ['Pelayanan', 'Kemarin saya menunggu di loket cukup lama karena petugas sedang istirahat bersamaan. Mohon diatur bergantian.', null, 'baru', null],
            ['Sosial', 'Ada lansia sebatang kara di RT 004 yang belum menerima bantuan sembako. Mohon didata.', 'RT 004/RW 002', 'ditanggapi', 'Petugas kesejahteraan sosial akan melakukan kunjungan dan pendataan pada Kamis ini.'],
        ];
        foreach ($aduan as $i => [$kat, $isi, $lokasi, $status, $tanggapan]) {
            Pengaduan::create(['kode' => 'ADU-' . strtoupper(Str::random(6)), 'nama' => ['Hendra Saputra', 'Dewi Rahayu', 'Agus Hidayat', 'Lina Maharani', 'Rudi Gunawan'][$i], 'kontak' => '0813' . mt_rand(10000000, 99999999), 'kategori' => $kat, 'lokasi' => $lokasi, 'isi' => $isi, 'status' => $status, 'tanggapan' => $tanggapan, 'ditanggapi_at' => $tanggapan ? now()->subDays($i + 1) : null, 'created_at' => now()->subDays($i * 3 + 2), 'updated_at' => now()]);
        }
    }

    private function layanan(): array
    {
        $penutup = "\nDemikian surat keterangan ini dibuat dengan sebenarnya untuk dapat dipergunakan sebagaimana mestinya.";
        return [
            ['kode' => 'SKD', 'nama' => 'Surat Keterangan Domisili', 'ikon' => 'rumah', 'estimasi_hari' => 1, 'ringkas' => 'Bukti tempat tinggal untuk melamar kerja, membuka rekening, atau sekolah.',
             'persyaratan' => "Fotokopi KTP\nFotokopi Kartu Keluarga\nSurat pengantar RT/RW", 'kolom' => '',
             'isi_surat' => "Orang tersebut di atas benar-benar penduduk yang berdomisili di {alamat}, Kelurahan {kelurahan}, Kecamatan {kecamatan}, {kota}.\nSurat keterangan ini diberikan untuk keperluan: {keperluan}." . $penutup],
            ['kode' => 'SKTM', 'nama' => 'Surat Keterangan Tidak Mampu', 'ikon' => 'dompet', 'estimasi_hari' => 2, 'ringkas' => 'Untuk keringanan biaya sekolah, beasiswa, atau layanan kesehatan.',
             'persyaratan' => "Fotokopi KTP\nFotokopi Kartu Keluarga\nSurat pengantar RT/RW\nFoto rumah tampak depan", 'kolom' => "penghasilan|Penghasilan per bulan (Rp)|wajib|number\ntanggungan|Jumlah tanggungan keluarga|wajib|number",
             'isi_surat' => "Berdasarkan surat pengantar RT/RW, orang tersebut di atas benar warga Kelurahan {kelurahan} yang tergolong keluarga kurang mampu dengan penghasilan sekitar Rp {penghasilan} per bulan dan {tanggungan} orang tanggungan.\nSurat keterangan ini diberikan untuk keperluan: {keperluan}." . $penutup],
            ['kode' => 'SKCK', 'nama' => 'Pengantar SKCK', 'ikon' => 'perisai', 'estimasi_hari' => 1, 'ringkas' => 'Surat pengantar untuk membuat SKCK di kantor polisi.',
             'persyaratan' => "Fotokopi KTP\nFotokopi Kartu Keluarga\nSurat pengantar RT/RW\nPas foto 4x6 latar merah (dibawa saat ke Polsek)", 'kolom' => '',
             'isi_surat' => "Sepanjang pengetahuan kami, orang tersebut di atas berkelakuan baik, tidak pernah tersangkut perkara pidana, dan tidak terlibat organisasi terlarang.\nSurat pengantar ini diberikan untuk mengurus Surat Keterangan Catatan Kepolisian (SKCK) dengan keperluan: {keperluan}." . $penutup],
            ['kode' => 'SKU', 'nama' => 'Surat Keterangan Usaha', 'ikon' => 'toko', 'estimasi_hari' => 2, 'ringkas' => 'Bukti memiliki usaha untuk pengajuan kredit atau izin usaha mikro.',
             'persyaratan' => "Fotokopi KTP\nFotokopi Kartu Keluarga\nFoto tempat usaha\nSurat pengantar RT/RW", 'kolom' => "nama_usaha|Nama usaha|wajib\njenis_usaha|Jenis usaha|wajib\nalamat_usaha|Alamat usaha|wajib\nmulai_usaha|Tahun mulai usaha|wajib|number",
             'isi_surat' => "Orang tersebut di atas benar memiliki usaha {jenis_usaha} dengan nama \"{nama_usaha}\" yang berlokasi di {alamat_usaha}, Kelurahan {kelurahan}, dan telah berjalan sejak tahun {mulai_usaha}.\nSurat keterangan ini diberikan untuk keperluan: {keperluan}." . $penutup],
            ['kode' => 'SKBM', 'nama' => 'Surat Keterangan Belum Menikah', 'ikon' => 'cincin', 'estimasi_hari' => 1, 'ringkas' => 'Pernyataan status belum menikah untuk syarat nikah atau melamar kerja.',
             'persyaratan' => "Fotokopi KTP\nFotokopi Kartu Keluarga\nSurat pernyataan belum menikah bermeterai\nSurat pengantar RT/RW", 'kolom' => '',
             'isi_surat' => "Berdasarkan data kependudukan dan surat pernyataan yang bersangkutan, orang tersebut di atas sampai saat ini berstatus belum pernah menikah.\nSurat keterangan ini diberikan untuk keperluan: {keperluan}." . $penutup],
            ['kode' => 'SKL', 'nama' => 'Surat Keterangan Kelahiran', 'ikon' => 'bayi', 'estimasi_hari' => 1, 'ringkas' => 'Pengantar pembuatan akta kelahiran di Dukcapil.',
             'persyaratan' => "Surat keterangan lahir dari bidan/rumah sakit\nFotokopi KTP kedua orang tua\nFotokopi Kartu Keluarga\nFotokopi buku nikah", 'kolom' => "nama_bayi|Nama bayi|wajib\njenis_kelamin_bayi|Jenis kelamin bayi|wajib\ntempat_lahir_bayi|Tempat lahir bayi|wajib\ntanggal_lahir_bayi|Tanggal lahir bayi|wajib|date\nnama_ibu|Nama ibu|wajib",
             'isi_surat' => "Menerangkan bahwa telah lahir seorang anak {jenis_kelamin_bayi} bernama {nama_bayi} di {tempat_lahir_bayi} pada tanggal {tanggal_lahir_bayi}, dari seorang ibu bernama {nama_ibu} dan ayah/pelapor sebagaimana identitas di atas.\nSurat keterangan ini diberikan untuk keperluan: {keperluan}." . $penutup],
            ['kode' => 'SKM', 'nama' => 'Surat Keterangan Kematian', 'ikon' => 'bunga', 'estimasi_hari' => 1, 'ringkas' => 'Pengantar akta kematian, klaim asuransi, atau ahli waris.',
             'persyaratan' => "Surat keterangan kematian dari RS/RT\nFotokopi KTP almarhum/almarhumah\nFotokopi Kartu Keluarga\nFotokopi KTP pelapor", 'kolom' => "nama_almarhum|Nama almarhum/almarhumah|wajib\ntanggal_meninggal|Tanggal meninggal|wajib|date\ntempat_meninggal|Tempat meninggal|wajib\nsebab|Sebab meninggal|\nhubungan|Hubungan pelapor dengan almarhum|wajib",
             'isi_surat' => "Menerangkan bahwa {nama_almarhum} telah meninggal dunia pada tanggal {tanggal_meninggal} di {tempat_meninggal} karena {sebab}. Pelapor sebagaimana identitas di atas merupakan {hubungan} dari almarhum/almarhumah.\nSurat keterangan ini diberikan untuk keperluan: {keperluan}." . $penutup],
            ['kode' => 'SKP', 'nama' => 'Surat Pengantar Pindah', 'ikon' => 'pindah', 'estimasi_hari' => 3, 'ringkas' => 'Pengantar pindah domisili keluar kelurahan.',
             'persyaratan' => "KTP asli\nKartu Keluarga asli\nSurat pengantar RT/RW\nPas foto 3x4 (2 lembar)", 'kolom' => "alamat_tujuan|Alamat tujuan lengkap|wajib|textarea\nalasan_pindah|Alasan pindah|wajib\njumlah_pengikut|Jumlah anggota keluarga yang ikut pindah|wajib|number",
             'isi_surat' => "Orang tersebut di atas bermaksud pindah ke {alamat_tujuan} dengan alasan {alasan_pindah}, beserta {jumlah_pengikut} anggota keluarga yang ikut pindah.\nSurat pengantar ini diberikan untuk keperluan: {keperluan}." . $penutup],
        ];
    }

    private function kabar(): array
    {
        return [
            ['Posyandu Balita dan Lansia Serentak Sabtu Ini di 5 RW', 'Kesehatan', "Pemerintah Kelurahan Sukamaju bersama kader PKK menggelar posyandu balita dan lansia serentak di lima RW pada Sabtu pukul 08.00 sampai 11.00.\n\nLayanan yang tersedia meliputi penimbangan, imunisasi, pemberian vitamin A, cek tekanan darah, gula darah, dan konsultasi gizi. Warga diminta membawa buku KIA dan kartu identitas.\n\nInformasi lebih lanjut dapat ditanyakan kepada kader posyandu di masing-masing RW."],
            ['Layanan Surat Online Kini Bisa Diunduh Langsung dalam PDF', 'Informasi Layanan', "Mulai bulan ini, surat keterangan yang diajukan secara online dapat langsung diunduh dalam bentuk PDF begitu statusnya selesai.\n\nSetiap surat dilengkapi nomor resmi dan kode QR. Instansi penerima surat cukup memindai kode QR untuk memastikan surat tersebut asli diterbitkan oleh kelurahan.\n\nWarga yang belum memiliki akun dapat mendaftar memakai NIK dan tanggal lahir yang terdaftar di kelurahan."],
            ['Pendataan Ulang Penerima Bantuan Sosial Tahun 2027', 'Bantuan Sosial', "Kelurahan melakukan pendataan ulang calon penerima bantuan sosial untuk tahun 2027. Ketua RT akan mendatangi rumah warga yang masuk usulan.\n\nWarga yang merasa layak namun belum terdata dapat mengajukan diri melalui ketua RT atau datang langsung ke kantor kelurahan dengan membawa KTP dan KK.\n\nPendataan berlangsung hingga akhir bulan depan."],
            ['Kerja Bakti Bersih Saluran Air Menjelang Musim Hujan', 'Kegiatan', "Dalam rangka mengantisipasi banjir, kelurahan mengajak seluruh warga mengikuti kerja bakti membersihkan saluran air pada Minggu pagi.\n\nTitik kumpul di masing-masing pos RW pukul 07.00. Peralatan seperti cangkul dan karung disediakan kelurahan bekerja sama dengan LPM."],
            ['Jam Layanan Loket Selama Libur Nasional', 'Pengumuman', "Selama libur nasional dan cuti bersama, loket pelayanan kelurahan tutup. Permohonan surat tetap bisa diajukan secara online dan akan diproses pada hari kerja berikutnya.\n\nUntuk keadaan mendesak seperti surat keterangan kematian, warga dapat menghubungi nomor WhatsApp kelurahan."],
        ];
    }
}
