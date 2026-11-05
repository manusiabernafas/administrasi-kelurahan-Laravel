<?php

use App\Http\Controllers\Admin;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BerkasController;
use App\Http\Controllers\PublikController;
use App\Http\Controllers\WargaController;
use Illuminate\Support\Facades\Route;

// Halaman publik
Route::controller(PublikController::class)->group(function () {
    Route::get('/', 'beranda')->name('beranda');
    Route::get('/layanan', 'layanan')->name('layanan');
    Route::get('/layanan/{kode}', 'layananDetail')->name('layanan.detail');
    Route::get('/cek-status', 'cekStatus')->name('cek-status');
    Route::get('/verifikasi/{kode}', 'verifikasi')->name('verifikasi');
    Route::get('/kabar', 'kabar')->name('kabar');
    Route::get('/kabar/{slug}', 'kabarDetail')->name('kabar.detail');
    Route::get('/pengaduan', 'pengaduan')->name('pengaduan');
    Route::post('/pengaduan', 'kirimPengaduan')->middleware('throttle:pengaduan')->name('pengaduan.kirim');
});

// Masuk & daftar
Route::middleware('guest')->controller(AuthController::class)->group(function () {
    Route::get('/masuk', 'masukForm')->name('masuk');
    Route::post('/masuk', 'masuk')->name('masuk.proses');
    Route::get('/daftar', 'daftarForm')->name('daftar');
    Route::post('/daftar', 'daftar')->middleware('throttle:daftar')->name('daftar.proses');
});
Route::post('/keluar', [AuthController::class, 'keluar'])->middleware('auth')->name('keluar');
Route::get('/login', fn () => redirect()->route('masuk'))->name('login');

// Berkas lampiran & surat (dicek kepemilikannya)
Route::middleware('auth')->controller(BerkasController::class)->group(function () {
    Route::get('/berkas/{permohonan}/lampiran/{i}', 'lampiran')->whereNumber(['permohonan', 'i'])->name('berkas.lampiran');
    Route::get('/berkas/{permohonan}/surat', 'surat')->whereNumber('permohonan')->name('berkas.surat');
});

// Akun warga
Route::middleware(['auth', 'peran:warga'])->prefix('akun')->name('warga.')->controller(WargaController::class)->group(function () {
    Route::get('/', 'beranda')->name('beranda');
    Route::get('/ajukan', 'pilih')->name('pilih');
    Route::get('/ajukan/{kode}', 'ajukan')->name('ajukan');
    Route::post('/ajukan/{kode}', 'simpan')->middleware('throttle:ajukan')->name('simpan');
    Route::get('/permohonan/{id}', 'detail')->whereNumber('id')->name('detail');
    Route::post('/permohonan/{id}/batal', 'batal')->whereNumber('id')->name('batal');
    Route::get('/profil', 'profil')->name('profil');
    Route::post('/profil', 'simpanProfil')->name('profil.simpan');
    Route::post('/sandi', 'sandi')->name('sandi');
});

// Dasbor petugas & admin
Route::middleware(['auth', 'peran:admin,petugas'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [Admin\DasborController::class, 'index'])->name('dasbor');

    Route::controller(Admin\PermohonanController::class)->prefix('permohonan')->name('permohonan.')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('/baru', 'create')->name('create');
        Route::post('/', 'store')->name('store');
        Route::get('/cari-penduduk', 'cariPenduduk')->name('cari-penduduk');
        Route::get('/{permohonan}', 'show')->whereNumber('permohonan')->name('show');
        Route::post('/{permohonan}/status', 'status')->whereNumber('permohonan')->name('status');
        Route::get('/{permohonan}/pratinjau', 'pratinjau')->whereNumber('permohonan')->name('pratinjau');
    });

    Route::get('penduduk/template', [Admin\PendudukController::class, 'template'])->name('penduduk.template');
    Route::post('penduduk/impor', [Admin\PendudukController::class, 'impor'])->name('penduduk.impor');
    Route::resource('penduduk', Admin\PendudukController::class)->except('show')->parameters(['penduduk' => 'penduduk']);
    Route::get('penduduk/{penduduk}', [Admin\PendudukController::class, 'show'])->name('penduduk.show');

    Route::resource('layanan', Admin\LayananController::class)->except('show')->parameters(['layanan' => 'layanan']);
    Route::resource('kabar', Admin\PengumumanController::class)->except('show')->parameters(['kabar' => 'pengumuman']);

    Route::get('pengaduan', [Admin\PengaduanController::class, 'index'])->name('pengaduan.index');
    Route::get('pengaduan/{pengaduan}', [Admin\PengaduanController::class, 'show'])->name('pengaduan.show');
    Route::post('pengaduan/{pengaduan}', [Admin\PengaduanController::class, 'tanggapi'])->name('pengaduan.tanggapi');

    Route::get('laporan', [Admin\LaporanController::class, 'index'])->name('laporan');
    Route::get('laporan/csv', [Admin\LaporanController::class, 'csv'])->name('laporan.csv');

    Route::get('profil', [Admin\ProfilController::class, 'index'])->name('profil');
    Route::post('profil', [Admin\ProfilController::class, 'sandi'])->name('profil.sandi');

    Route::middleware('peran:admin')->group(function () {
        Route::get('pengguna', [Admin\PenggunaController::class, 'index'])->name('pengguna.index');
        Route::post('pengguna', [Admin\PenggunaController::class, 'store'])->name('pengguna.store');
        Route::put('pengguna/{user}', [Admin\PenggunaController::class, 'update'])->name('pengguna.update');
        Route::post('pengguna/{user}/reset', [Admin\PenggunaController::class, 'reset'])->name('pengguna.reset');
        Route::get('pengaturan', [Admin\PengaturanController::class, 'index'])->name('pengaturan');
        Route::post('pengaturan', [Admin\PengaturanController::class, 'simpan'])->name('pengaturan.simpan');
    });
});
