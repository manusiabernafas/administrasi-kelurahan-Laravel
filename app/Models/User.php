<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = ['name', 'nik', 'peran', 'telepon', 'aktif', 'email', 'password'];

    protected $hidden = ['password', 'remember_token'];

    protected function casts(): array
    {
        return ['password' => 'hashed', 'aktif' => 'boolean'];
    }

    public function staf(): bool
    {
        return in_array($this->peran, ['admin', 'petugas'], true);
    }

    public function penduduk()
    {
        return $this->hasOne(Penduduk::class, 'nik', 'nik');
    }
}
