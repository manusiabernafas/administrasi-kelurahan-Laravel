<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Support\Kelurahan;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PengaturanController extends Controller
{
    public function index()
    {
        return view('admin.pengaturan', ['k' => Kelurahan::semua()]);
    }

    public function simpan(Request $r)
    {
        $d = $r->validate([
            'nama' => 'required|string|max:80', 'kecamatan' => 'required|string|max:80', 'kota' => 'required|string|max:80', 'provinsi' => 'required|string|max:80',
            'kode_pos' => 'nullable|digits:5', 'alamat' => 'required|string|max:200', 'telepon' => 'nullable|string|max:30', 'whatsapp' => ['nullable', 'regex:/^[0-9+ ]{8,20}$/'],
            'email' => 'nullable|email|max:120', 'jam_layanan' => 'required|string|max:300', 'nama_lurah' => 'required|string|max:120', 'nip_lurah' => 'nullable|string|max:40',
            'kode_surat' => ['required', 'regex:/^[A-Z0-9.\-]{2,20}$/'], 'tagline' => 'nullable|string|max:120', 'sambutan' => 'nullable|string|max:600',
            'peta' => 'nullable|url:https|max:1000', 'logo' => 'nullable|image|mimes:png,jpg,jpeg|max:1024',
        ], ['kode_surat.regex' => 'Kode surat hanya huruf besar, angka, titik, atau strip (contoh KL-SKM).', 'whatsapp.regex' => 'Nomor WhatsApp tidak valid.']);
        unset($d['logo']);
        if ($r->hasFile('logo')) {
            $nama = 'logo-' . Str::random(8) . '.' . $r->file('logo')->extension();
            $r->file('logo')->move(public_path('uploads'), $nama);
            if (Kelurahan::get('logo')) {
                @unlink(public_path('uploads/' . Kelurahan::get('logo')));
            }
            $d['logo'] = $nama;
        }
        Kelurahan::simpan(array_map(fn ($v) => $v ?? '', $d));
        return back()->with('ok', 'Pengaturan disimpan.');
    }
}
