<?php

namespace App\Http\Controllers;

use App\Models\Penduduk;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password;

class AuthController extends Controller
{
    public function masukForm()
    {
        return view('auth.masuk');
    }

    /** Warga masuk dengan NIK, petugas dengan email. */
    public function masuk(Request $r)
    {
        $d = $r->validate(['akun' => 'required|string|max:120', 'password' => 'required|string|max:100'], [], ['akun' => 'NIK / email']);
        $akun = trim($d['akun']);
        $kunci = 'masuk:' . Str::lower($akun) . '|' . $r->ip();
        if (RateLimiter::tooManyAttempts($kunci, 5)) {
            return back()->withInput($r->only('akun'))->with('error', 'Terlalu banyak percobaan. Coba lagi dalam ' . RateLimiter::availableIn($kunci) . ' detik.');
        }
        $kolom = str_contains($akun, '@') ? 'email' : 'nik';
        if (! Auth::attempt([$kolom => $akun, 'password' => $d['password'], 'aktif' => true], $r->boolean('ingat'))) {
            RateLimiter::hit($kunci, 60);
            return back()->withInput($r->only('akun'))->with('error', 'NIK/email atau kata sandi salah.');
        }
        RateLimiter::clear($kunci);
        $r->session()->regenerate();
        return redirect()->intended(Auth::user()->staf() ? route('admin.dasbor') : route('warga.beranda'));
    }

    public function daftarForm()
    {
        return view('auth.daftar');
    }

    /** Daftar akun warga: NIK harus terdaftar sebagai penduduk & dicocokkan dengan tanggal lahir. */
    public function daftar(Request $r)
    {
        $d = $r->validate([
            'nik' => 'required|digits:16', 'tanggal_lahir' => 'required|date',
            'telepon' => ['required', 'regex:/^(\+62|62|0)8[0-9]{7,12}$/'],
            'password' => ['required', 'confirmed', Password::min(8)->letters()->numbers()],
        ], ['telepon.regex' => 'Nomor HP tidak valid (contoh 081234567890).'], ['nik' => 'NIK', 'telepon' => 'nomor HP']);
        $p = Penduduk::where('nik', $d['nik'])->where('status', 'aktif')->first();
        if (! $p || $p->tanggal_lahir->toDateString() !== date('Y-m-d', strtotime($d['tanggal_lahir']))) {
            return back()->withInput($r->except('password', 'password_confirmation'))->withErrors(['nik' => 'NIK dan tanggal lahir tidak cocok dengan data penduduk kelurahan. Hubungi petugas bila data Anda belum terdaftar.']);
        }
        if (User::where('nik', $d['nik'])->exists()) {
            return back()->withInput($r->except('password', 'password_confirmation'))->withErrors(['nik' => 'NIK ini sudah punya akun. Silakan masuk.']);
        }
        $u = User::create(['name' => $p->nama, 'nik' => $p->nik, 'peran' => 'warga', 'telepon' => $d['telepon'], 'password' => $d['password'], 'aktif' => true]);
        Auth::login($u);
        $r->session()->regenerate();
        return redirect()->route('warga.beranda')->with('ok', 'Akun berhasil dibuat. Selamat datang, ' . $p->nama . '!');
    }

    public function keluar(Request $r)
    {
        Auth::logout();
        $r->session()->invalidate();
        $r->session()->regenerateToken();
        return redirect()->route('masuk')->with('ok', 'Anda sudah keluar.');
    }
}
