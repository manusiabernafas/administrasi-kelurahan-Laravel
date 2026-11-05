@extends('publik.layout', ['judul' => 'Daftar akun warga'])
@section('hero')<div class="judul"><span class="kicker">Akun warga</span><h1>Daftar sekali, <span class="serif">urus surat kapan saja</span></h1><p>NIK dan tanggal lahir dicocokkan dengan data kependudukan kelurahan.</p></div>@endsection
@section('isi')
<div class="wadah konten" style="max-width:560px">
  <form class="form panel putih" method="post" action="{{ route('daftar.proses') }}" novalidate>
    @csrf
    <div class="f"><label for="nik">NIK (16 digit)</label><input id="nik" name="nik" value="{{ old('nik') }}" inputmode="numeric" maxlength="16" required>@error('nik')<div class="galat">{{ $message }}</div>@enderror</div>
    <div class="dua">
      <div class="f"><label for="tanggal_lahir">Tanggal lahir</label><input id="tanggal_lahir" type="date" name="tanggal_lahir" value="{{ old('tanggal_lahir') }}" required>@error('tanggal_lahir')<div class="galat">{{ $message }}</div>@enderror</div>
      <div class="f"><label for="telepon">Nomor HP / WhatsApp</label><input id="telepon" name="telepon" value="{{ old('telepon') }}" inputmode="tel" placeholder="0812..." required>@error('telepon')<div class="galat">{{ $message }}</div>@enderror</div>
    </div>
    <div class="dua">
      <div class="f"><label for="password">Kata sandi</label><input id="password" type="password" name="password" autocomplete="new-password" required>@error('password')<div class="galat">{{ $message }}</div>@enderror</div>
      <div class="f"><label for="password_confirmation">Ulangi kata sandi</label><input id="password_confirmation" type="password" name="password_confirmation" autocomplete="new-password" required></div>
    </div>
    <p class="kecil muted" style="margin:-4px 0 16px">Minimal 8 karakter, berisi huruf dan angka.</p>
    <button class="btn gelap" style="width:100%;justify-content:space-between">Buat akun <span class="bulat">{!! ikon('panah') !!}</span></button>
    <p class="kecil muted" style="text-align:center;margin:18px 0 0">Sudah punya akun? <a href="{{ route('masuk') }}"><b>Masuk</b></a></p>
  </form>
</div>
@endsection
