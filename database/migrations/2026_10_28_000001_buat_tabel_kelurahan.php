<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/** Semua tabel layanan administrasi kelurahan. */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $t) {
            $t->string('email')->nullable()->change();
            $t->string('nik', 16)->nullable()->unique()->after('name');
            $t->enum('peran', ['admin', 'petugas', 'warga'])->default('warga')->after('nik');
            $t->string('telepon', 20)->nullable()->after('peran');
            $t->boolean('aktif')->default(true)->after('telepon');
        });

        Schema::create('pengaturan', function (Blueprint $t) {
            $t->string('kunci', 60)->primary();
            $t->text('nilai')->nullable();
        });

        Schema::create('penduduk', function (Blueprint $t) {
            $t->id();
            $t->string('nik', 16)->unique();
            $t->string('no_kk', 16)->index();
            $t->string('nama', 120);
            $t->string('tempat_lahir', 80);
            $t->date('tanggal_lahir');
            $t->enum('jenis_kelamin', ['L', 'P']);
            $t->string('agama', 20);
            $t->string('status_perkawinan', 30);
            $t->string('pekerjaan', 80)->nullable();
            $t->string('pendidikan', 40)->nullable();
            $t->string('alamat', 200);
            $t->string('rt', 3);
            $t->string('rw', 3);
            $t->string('kewarganegaraan', 10)->default('WNI');
            $t->enum('status', ['aktif', 'pindah', 'meninggal'])->default('aktif');
            $t->timestamps();
        });

        Schema::create('layanan', function (Blueprint $t) {
            $t->id();
            $t->string('kode', 10)->unique();
            $t->string('nama', 120);
            $t->string('ringkas', 255);
            $t->text('persyaratan');
            $t->text('kolom')->nullable();      // isian tambahan: satu baris "nama|Label|wajib"
            $t->text('isi_surat');               // paragraf surat, boleh pakai {placeholder}
            $t->unsignedTinyInteger('estimasi_hari')->default(1);
            $t->string('ikon', 30)->default('dokumen');
            $t->boolean('aktif')->default(true);
            $t->unsignedSmallInteger('urutan')->default(0);
            $t->timestamps();
        });

        Schema::create('permohonan', function (Blueprint $t) {
            $t->id();
            $t->string('kode', 20)->unique();             // kode lacak untuk warga
            $t->foreignId('layanan_id')->constrained('layanan');
            $t->foreignId('penduduk_id')->constrained('penduduk');
            $t->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();   // pemohon (akun warga)
            $t->foreignId('petugas_id')->nullable()->constrained('users')->nullOnDelete();
            $t->string('keperluan', 255);
            $t->json('data')->nullable();
            $t->json('lampiran')->nullable();
            $t->enum('status', ['diajukan', 'diverifikasi', 'diproses', 'selesai', 'ditolak'])->default('diajukan')->index();
            $t->text('catatan')->nullable();
            $t->string('nomor_surat', 60)->nullable()->unique();
            $t->date('tanggal_surat')->nullable();
            $t->string('kode_verifikasi', 40)->nullable()->unique();
            $t->enum('saluran', ['online', 'loket'])->default('online');
            $t->dateTime('selesai_at')->nullable();
            $t->timestamps();
        });

        Schema::create('riwayat_permohonan', function (Blueprint $t) {
            $t->id();
            $t->foreignId('permohonan_id')->constrained('permohonan')->cascadeOnDelete();
            $t->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $t->string('status', 20);
            $t->string('catatan', 500)->nullable();
            $t->timestamp('created_at')->useCurrent();
        });

        Schema::create('pengumuman', function (Blueprint $t) {
            $t->id();
            $t->string('judul', 200);
            $t->string('slug', 220)->unique();
            $t->string('kategori', 40)->default('Pengumuman');
            $t->text('ringkas')->nullable();
            $t->longText('isi');
            $t->string('gambar')->nullable();
            $t->boolean('terbit')->default(true);
            $t->dateTime('terbit_at')->nullable();
            $t->unsignedInteger('dilihat')->default(0);
            $t->timestamps();
        });

        Schema::create('pengaduan', function (Blueprint $t) {
            $t->id();
            $t->string('kode', 20)->unique();
            $t->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $t->string('nama', 120);
            $t->string('kontak', 120);
            $t->string('kategori', 40);
            $t->string('lokasi', 200)->nullable();
            $t->text('isi');
            $t->string('lampiran')->nullable();
            $t->enum('status', ['baru', 'ditanggapi', 'selesai'])->default('baru')->index();
            $t->text('tanggapan')->nullable();
            $t->dateTime('ditanggapi_at')->nullable();
            $t->timestamps();
        });
    }

    public function down(): void
    {
        foreach (['pengaduan', 'pengumuman', 'riwayat_permohonan', 'permohonan', 'layanan', 'penduduk', 'pengaturan'] as $t) {
            Schema::dropIfExists($t);
        }
        Schema::table('users', fn (Blueprint $t) => $t->dropColumn(['nik', 'peran', 'telepon', 'aktif']));
    }
};
