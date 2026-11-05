<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Penduduk extends Model
{
    protected $table = 'penduduk';
    protected $guarded = ['id'];
    protected $casts = ['tanggal_lahir' => 'date'];

    public function getUmurAttribute(): int
    {
        return $this->tanggal_lahir->age;
    }

    public function getTtlAttribute(): string
    {
        return $this->tempat_lahir . ', ' . tgl($this->tanggal_lahir);
    }

    public function getAlamatLengkapAttribute(): string
    {
        return $this->alamat . ' RT ' . $this->rt . ' / RW ' . $this->rw;
    }

    public function permohonan()
    {
        return $this->hasMany(Permohonan::class);
    }
}
