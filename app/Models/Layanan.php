<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Layanan extends Model
{
    protected $table = 'layanan';
    protected $guarded = ['id'];
    protected $casts = ['aktif' => 'boolean'];

    /** Daftar persyaratan (satu baris = satu syarat). */
    public function permohonan()
    {
        return $this->hasMany(Permohonan::class);
    }

    public function getSyaratAttribute(): array
    {
        return array_values(array_filter(array_map('trim', preg_split('/\R/', (string) $this->persyaratan))));
    }

    /** Isian tambahan: baris "nama|Label|wajib|tipe" → [['nama','label','wajib','tipe']]. */
    public function getIsianAttribute(): array
    {
        $out = [];
        foreach (preg_split('/\R/', (string) $this->kolom) as $baris) {
            $p = array_map('trim', explode('|', $baris));
            if (! preg_match('/^[a-z_][a-z0-9_]{0,40}$/', $p[0] ?? '')) {
                continue;
            }
            $out[] = ['nama' => $p[0], 'label' => $p[1] ?? $p[0], 'wajib' => ($p[2] ?? '') === 'wajib', 'tipe' => in_array($p[3] ?? '', ['date', 'number', 'textarea'], true) ? $p[3] : 'text'];
        }
        return $out;
    }
}
