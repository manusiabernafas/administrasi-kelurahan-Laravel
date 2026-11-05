<?php

namespace App\Http\Controllers;

use App\Models\Permohonan;
use App\Support\Surat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

/** Lampiran & surat hanya bisa dibuka pemohon sendiri atau petugas. */
class BerkasController extends Controller
{
    private function izin(Request $r, Permohonan $p): void
    {
        $u = $r->user();
        abort_unless($u->staf() || $p->user_id === $u->id, 403);
    }

    public function lampiran(Request $r, Permohonan $permohonan, int $i)
    {
        $this->izin($r, $permohonan);
        $f = ($permohonan->lampiran ?? [])[$i] ?? null;
        abort_unless($f && Storage::disk('local')->exists($f['path']), 404);
        return Storage::disk('local')->response($f['path'], $f['nama']);
    }

    public function surat(Request $r, Permohonan $permohonan)
    {
        $this->izin($r, $permohonan);
        abort_unless($permohonan->status === 'selesai' && $permohonan->nomor_surat, 404);
        $nama = str_replace('/', '-', $permohonan->nomor_surat) . '.pdf';
        return Surat::pdf($permohonan)->download($nama);
    }
}
