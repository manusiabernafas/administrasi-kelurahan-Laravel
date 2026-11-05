<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pengaduan extends Model
{
    public const KATEGORI = ['Infrastruktur', 'Kebersihan', 'Keamanan', 'Pelayanan', 'Sosial', 'Lainnya'];
    public const STATUS = ['baru' => ['Baru', 'abu'], 'ditanggapi' => ['Ditanggapi', 'kuning'], 'selesai' => ['Selesai', 'hijau']];

    protected $table = 'pengaduan';
    protected $guarded = ['id'];
    protected $casts = ['ditanggapi_at' => 'datetime'];
}
