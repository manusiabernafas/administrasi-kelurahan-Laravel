<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

/** Batasi route ke peran tertentu, mis. peran:admin,petugas */
class Peran
{
    public function handle(Request $r, Closure $next, string ...$peran)
    {
        $u = $r->user();
        if (! $u || ! $u->aktif) {
            return redirect()->route('masuk');
        }
        if (! in_array($u->peran, $peran, true)) {
            return $u->staf() ? redirect()->route('admin.dasbor') : redirect()->route('warga.beranda');
        }
        return $next($r);
    }
}
