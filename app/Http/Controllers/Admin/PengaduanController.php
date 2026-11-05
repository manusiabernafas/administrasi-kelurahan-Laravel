<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pengaduan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PengaduanController extends Controller
{
    public function index(Request $r)
    {
        $q = Pengaduan::latest();
        if ($s = $r->query('status')) {
            $q->where('status', $s);
        }
        return view('admin.pengaduan.index', ['daftar' => $q->paginate(15)->withQueryString(), 'hitung' => Pengaduan::selectRaw('status, COUNT(*) n')->groupBy('status')->pluck('n', 'status')]);
    }

    public function show(Request $r, Pengaduan $pengaduan)
    {
        if ($r->query('lampiran') && $pengaduan->lampiran && Storage::disk('local')->exists($pengaduan->lampiran)) {
            return Storage::disk('local')->response($pengaduan->lampiran);
        }
        return view('admin.pengaduan.show', ['a' => $pengaduan]);
    }

    public function tanggapi(Request $r, Pengaduan $pengaduan)
    {
        $d = $r->validate(['tanggapan' => 'required|string|min:5|max:3000', 'status' => 'required|in:ditanggapi,selesai']);
        $pengaduan->update($d + ['ditanggapi_at' => now()]);
        return back()->with('ok', 'Tanggapan disimpan. Pelapor bisa melihatnya lewat kode ' . $pengaduan->kode . '.');
    }
}
