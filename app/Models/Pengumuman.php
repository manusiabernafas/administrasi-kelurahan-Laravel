<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pengumuman extends Model
{
    protected $table = 'pengumuman';
    protected $guarded = ['id'];
    protected $casts = ['terbit' => 'boolean', 'terbit_at' => 'datetime'];

    public function scopeTampil($q)
    {
        return $q->where('terbit', true)->where('terbit_at', '<=', now());
    }
}
