<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class PenggunaController extends Controller
{
    public function index(Request $r)
    {
        $warga = User::where('peran', 'warga')->when($r->query('q'), fn ($q, $c) => $q->where(fn ($w) => $w->where('name', 'like', "%{$c}%")->orWhere('nik', 'like', "%{$c}%")))->latest()->paginate(15)->withQueryString();
        return view('admin.pengguna', ['staf' => User::whereIn('peran', ['admin', 'petugas'])->orderBy('peran')->orderBy('name')->get(), 'warga' => $warga]);
    }

    public function store(Request $r)
    {
        $d = $r->validate(['name' => 'required|string|max:120', 'email' => 'required|email|max:150|unique:users,email', 'peran' => 'required|in:admin,petugas', 'telepon' => 'nullable|string|max:20']);
        $sandi = Str::password(10, symbols: false);
        User::create($d + ['password' => $sandi, 'aktif' => true]);
        return back()->with('ok', 'Akun petugas dibuat.')->with('sandi_baru', [$d['email'], $sandi]);
    }

    public function update(Request $r, User $user)
    {
        $d = $r->validate(['name' => 'required|string|max:120', 'peran' => 'required|in:admin,petugas,warga', 'aktif' => 'nullable|boolean', 'email' => ['nullable', 'email', 'max:150', Rule::unique('users', 'email')->ignore($user->id)]]);
        if ($user->id === $r->user()->id && ($d['peran'] !== 'admin' || ! $r->boolean('aktif'))) {
            return back()->with('error', 'Anda tidak bisa menurunkan peran atau menonaktifkan akun sendiri.');
        }
        if ($user->peran === 'warga') {
            $d['peran'] = 'warga'; // warga tidak bisa dijadikan staf dari sini
        }
        $user->update(['name' => $d['name'], 'peran' => $d['peran'], 'aktif' => $r->boolean('aktif'), 'email' => $d['email'] ?? $user->email]);
        return back()->with('ok', 'Akun diperbarui.');
    }

    /** Reset kata sandi: sandi baru ditampilkan sekali untuk diberikan ke pemilik akun. */
    public function reset(Request $r, User $user)
    {
        $sandi = Str::password(10, symbols: false);
        $user->update(['password' => $sandi]);
        return back()->with('ok', 'Kata sandi direset.')->with('sandi_baru', [$user->email ?: $user->nik, $sandi]);
    }
}
