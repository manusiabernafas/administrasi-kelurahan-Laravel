<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pengaduan;
use App\Models\Penduduk;
use App\Models\Permohonan;
use Illuminate\Support\Facades\DB;

class DasborController extends Controller
{
    public function index()
    {
        $per = Permohonan::select('status', DB::raw('COUNT(*) n'))->groupBy('status')->pluck('n', 'status');
        $hari = collect(range(13, 0))->map(fn ($i) => now()->subDays($i)->toDateString());
        $harian = Permohonan::where('created_at', '>=', now()->subDays(13)->startOfDay())->select(DB::raw('DATE(created_at) t'), DB::raw('COUNT(*) n'))->groupBy('t')->pluck('n', 't');
        return view('admin.dasbor', [
            'per' => $per,
            'menunggu' => Permohonan::with('layanan', 'penduduk')->whereIn('status', ['diajukan', 'diverifikasi', 'diproses'])->orderBy('created_at')->limit(8)->get(),
            'grafik' => $hari->map(fn ($t) => ['t' => $t, 'n' => (int) ($harian[$t] ?? 0)]),
            'terlaris' => Permohonan::join('layanan', 'layanan.id', '=', 'permohonan.layanan_id')->where('permohonan.created_at', '>=', now()->startOfMonth())->select('layanan.nama', DB::raw('COUNT(*) n'))->groupBy('layanan.nama')->orderByDesc('n')->limit(5)->get(),
            'penduduk' => Penduduk::where('status', 'aktif')->count(),
            'pengaduanBaru' => Pengaduan::where('status', 'baru')->count(),
            'selesaiHariIni' => Permohonan::where('status', 'selesai')->whereDate('selesai_at', today())->count(),
            'masukHariIni' => Permohonan::whereDate('created_at', today())->count(),
        ]);
    }
}
