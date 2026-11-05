<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Permohonan extends Model
{
    public const STATUS = [
        'diajukan' => ['Diajukan', 'abu'],
        'diverifikasi' => ['Diverifikasi', 'biru'],
        'diproses' => ['Diproses', 'kuning'],
        'selesai' => ['Selesai', 'hijau'],
        'ditolak' => ['Ditolak', 'merah'],
    ];

    protected $table = 'permohonan';
    protected $guarded = ['id'];
    protected $casts = ['data' => 'array', 'lampiran' => 'array', 'tanggal_surat' => 'date', 'selesai_at' => 'datetime'];

    public function layanan()
    {
        return $this->belongsTo(Layanan::class);
    }

    public function penduduk()
    {
        return $this->belongsTo(Penduduk::class);
    }

    public function pemohon()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function petugas()
    {
        return $this->belongsTo(User::class, 'petugas_id');
    }

    public function riwayat()
    {
        return $this->hasMany(RiwayatPermohonan::class)->orderBy('created_at')->orderBy('id');
    }

    public function catat(string $status, ?string $catatan = null, ?int $userId = null): void
    {
        $this->riwayat()->create(['status' => $status, 'catatan' => $catatan, 'user_id' => $userId, 'created_at' => now()]);
    }

    public function getLabelStatusAttribute(): string
    {
        return self::STATUS[$this->status][0] ?? $this->status;
    }

    public function getWarnaStatusAttribute(): string
    {
        return self::STATUS[$this->status][1] ?? 'abu';
    }
}
