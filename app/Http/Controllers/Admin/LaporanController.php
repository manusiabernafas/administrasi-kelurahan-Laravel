<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Layanan;
use App\Models\Permohonan;
use Illuminate\Http\Request;

class LaporanController extends Controller
{
    private function periode(Request $r): array
    {
        $bulan = preg_match('/^\d{4}-\d{2}$/', (string) $r->query('bulan')) ? $r->query('bulan') : now()->format('Y-m');
        $awal = \Carbon\Carbon::createFromFormat('Y-m-d', $bulan . '-01')->startOfDay();
        return [$bulan, $awal, $awal->copy()->endOfMonth()];
    }

    public function index(Request $r)
    {
        [$bulan, $awal, $akhir] = $this->periode($r);
        $rekap = Layanan::orderBy('urutan')->get()->map(function ($l) use ($awal, $akhir) {
            $q = Permohonan::where('layanan_id', $l->id)->whereBetween('created_at', [$awal, $akhir]);
            return ['nama' => $l->nama, 'kode' => $l->kode, 'masuk' => (clone $q)->count(), 'selesai' => (clone $q)->where('status', 'selesai')->count(), 'ditolak' => (clone $q)->where('status', 'ditolak')->count(), 'online' => (clone $q)->where('saluran', 'online')->count()];
        });
        return view('admin.laporan', [
            'bulan' => $bulan, 'rekap' => $rekap,
            'surat' => Permohonan::with('layanan', 'penduduk')->where('status', 'selesai')->whereBetween('tanggal_surat', [$awal->toDateString(), $akhir->toDateString()])->orderBy('nomor_surat')->get(),
        ]);
    }

    /** Buku register surat keluar (CSV, aman dari formula injection). */
    public function csv(Request $r)
    {
        [$bulan, $awal, $akhir] = $this->periode($r);
        $aman = fn ($v) => preg_match('/^[=+\-@\t\r]/', (string) $v) ? "'" . $v : (string) $v;
        $out = fopen('php://temp', 'r+');
        fwrite($out, "\xEF\xBB\xBF");
        fputcsv($out, ['No', 'Nomor Surat', 'Tanggal', 'Jenis', 'NIK', 'Nama', 'Keperluan', 'Saluran', 'Kode Lacak'], ';', '"', '');
        Permohonan::with('layanan', 'penduduk')->where('status', 'selesai')->whereBetween('tanggal_surat', [$awal->toDateString(), $akhir->toDateString()])->orderBy('nomor_surat')->get()
            ->each(function ($p, $i) use ($out, $aman) {
                fputcsv($out, array_map($aman, [$i + 1, $p->nomor_surat, $p->tanggal_surat->format('d/m/Y'), $p->layanan->nama, "'" . $p->penduduk->nik, $p->penduduk->nama, $p->keperluan, $p->saluran, $p->kode]), ';', '"', '');
            });
        rewind($out);
        return response(stream_get_contents($out), 200, ['Content-Type' => 'text/csv; charset=UTF-8', 'Content-Disposition' => 'attachment; filename="register-surat-' . $bulan . '.csv"']);
    }
}
