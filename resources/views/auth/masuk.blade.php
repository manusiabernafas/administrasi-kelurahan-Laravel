@extends('publik.layout', ['judul' => 'Masuk'])
@section('hero')<div class="judul"><span class="kicker">Akun warga & petugas</span><h1>Selamat <span class="serif">datang kembali</span></h1><p>Warga masuk memakai NIK. Petugas kelurahan masuk memakai email.</p></div>@endsection
@section('isi')
<div class="wadah konten" style="max-width:520px">
  <form class="form panel putih" method="post" action="{{ route('masuk.proses') }}" novalidate>
    @csrf
    @if (session('ok'))<div class="alert ok">{{ session('ok') }}</div>@endif
    @if (session('error'))<div class="alert err" role="alert">{{ session('error') }}</div>@endif
    <div class="f"><label for="akun">NIK atau email</label><input id="akun" name="akun" value="{{ old('akun') }}" autocomplete="username" required autofocus>@error('akun')<div class="galat">{{ $message }}</div>@enderror</div>
    <div class="f"><label for="password">Kata sandi</label><input id="password" type="password" name="password" autocomplete="current-password" required>@error('password')<div class="galat">{{ $message }}</div>@enderror</div>
    <div class="f"><label style="display:flex;gap:8px;align-items:center;font-weight:600"><input type="checkbox" name="ingat" value="1"> Ingat saya di perangkat ini</label></div>
    <button class="btn gelap" style="width:100%;justify-content:space-between">Masuk <span class="bulat">{!! ikon('panah') !!}</span></button>
    <p class="kecil muted" style="text-align:center;margin:18px 0 0">Belum punya akun? <a href="{{ route('daftar') }}"><b>Daftar dengan NIK</b></a><br>Lupa kata sandi? Hubungi petugas kelurahan untuk reset.</p>
  </form>
</div>
@endsection
