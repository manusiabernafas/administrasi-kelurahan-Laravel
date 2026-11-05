<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RiwayatPermohonan extends Model
{
    public $timestamps = false;
    protected $table = 'riwayat_permohonan';
    protected $guarded = ['id'];
    protected $casts = ['created_at' => 'datetime'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
