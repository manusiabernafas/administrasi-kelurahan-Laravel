<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pengumuman;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PengumumanController extends Controller
{
    public const KATEGORI = ['Pengumuman', 'Kegiatan', 'Informasi Layanan', 'Bantuan Sosial', 'Kesehatan'];

    public function index()
    {
        return view('admin.kabar.index', ['daftar' => Pengumuman::latest('terbit_at')->paginate(15)]);
    }

    private function data(Request $r, ?Pengumuman $k = null): array
    {
        $d = $r->validate([
            'judul' => 'required|string|max:200', 'kategori' => 'required|in:' . implode(',', self::KATEGORI), 'ringkas' => 'nullable|string|max:300',
            'isi' => 'required|string|max:30000', 'terbit_at' => 'nullable|date', 'gambar' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);
        $d['terbit'] = $r->boolean('terbit');
        $d['terbit_at'] = $d['terbit_at'] ?? ($k?->terbit_at ?? now());
        unset($d['gambar']);
        if ($r->hasFile('gambar')) {
            $nama = Str::random(20) . '.' . $r->file('gambar')->extension();
            $r->file('gambar')->move(public_path('uploads/kabar'), $nama);
            if ($k?->gambar) {
                @unlink(public_path('uploads/' . $k->gambar));
            }
            $d['gambar'] = 'kabar/' . $nama;
        }
        $dasar = Str::slug(Str::limit($d['judul'], 80, '')) ?: 'kabar';
        $slug = $dasar;
        for ($i = 2; Pengumuman::where('slug', $slug)->when($k, fn ($q) => $q->where('id', '!=', $k->id))->exists(); $i++) {
            $slug = $dasar . '-' . $i;
        }
        $d['slug'] = $slug;
        return $d;
    }

    public function create()
    {
        return view('admin.kabar.form', ['k' => new Pengumuman(['terbit' => true, 'kategori' => 'Pengumuman'])]);
    }

    public function store(Request $r)
    {
        Pengumuman::create($this->data($r));
        return redirect()->route('admin.kabar.index')->with('ok', 'Kabar diterbitkan.');
    }

    public function edit(Pengumuman $pengumuman)
    {
        return view('admin.kabar.form', ['k' => $pengumuman]);
    }

    public function update(Request $r, Pengumuman $pengumuman)
    {
        $pengumuman->update($this->data($r, $pengumuman));
        return redirect()->route('admin.kabar.index')->with('ok', 'Kabar diperbarui.');
    }

    public function destroy(Pengumuman $pengumuman)
    {
        if ($pengumuman->gambar) {
            @unlink(public_path('uploads/' . $pengumuman->gambar));
        }
        $pengumuman->delete();
        return back()->with('ok', 'Kabar dihapus.');
    }
}
