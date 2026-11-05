<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Layanan;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class LayananController extends Controller
{
    public const IKON = ['dokumen', 'rumah', 'dompet', 'perisai', 'toko', 'bayi', 'pindah', 'cincin', 'bunga', 'pengguna'];

    public function index()
    {
        return view('admin.layanan.index', ['daftar' => Layanan::withCount('permohonan')->orderBy('urutan')->get()]);
    }

    private function data(Request $r, ?Layanan $l = null): array
    {
        $d = $r->validate([
            'kode' => ['required', 'alpha_num:ascii', 'max:10', Rule::unique('layanan', 'kode')->ignore($l?->id)], 'nama' => 'required|string|max:120',
            'ringkas' => 'required|string|max:255', 'persyaratan' => 'required|string|max:3000', 'kolom' => 'nullable|string|max:2000',
            'isi_surat' => 'required|string|max:5000', 'estimasi_hari' => 'required|integer|between:0,30', 'ikon' => 'required|in:' . implode(',', self::IKON),
            'urutan' => 'nullable|integer|between:0,999',
        ], [], ['isi_surat' => 'isi surat', 'kolom' => 'isian tambahan']);
        $d['kode'] = strtoupper($d['kode']);
        $d['aktif'] = $r->boolean('aktif');
        $d['urutan'] = $d['urutan'] ?? 0;
        return $d;
    }

    public function create()
    {
        return view('admin.layanan.form', ['l' => new Layanan(['estimasi_hari' => 1, 'ikon' => 'dokumen', 'aktif' => true, 'isi_surat' => "Yang bertanda tangan di bawah ini Lurah {kelurahan}, Kecamatan {kecamatan}, {kota}, menerangkan bahwa warga tersebut di atas benar penduduk Kelurahan {kelurahan}.\nSurat keterangan ini dibuat untuk keperluan: {keperluan}.\nDemikian surat keterangan ini dibuat untuk dapat dipergunakan sebagaimana mestinya."])]);
    }

    public function store(Request $r)
    {
        Layanan::create($this->data($r));
        return redirect()->route('admin.layanan.index')->with('ok', 'Layanan ditambahkan.');
    }

    public function edit(Layanan $layanan)
    {
        return view('admin.layanan.form', ['l' => $layanan]);
    }

    public function update(Request $r, Layanan $layanan)
    {
        $layanan->update($this->data($r, $layanan));
        return redirect()->route('admin.layanan.index')->with('ok', 'Layanan diperbarui.');
    }

    public function destroy(Layanan $layanan)
    {
        if ($layanan->permohonan()->exists()) {
            return back()->with('error', 'Layanan sudah punya permohonan. Nonaktifkan saja (hapus centang Aktif).');
        }
        $layanan->delete();
        return back()->with('ok', 'Layanan dihapus.');
    }
}
