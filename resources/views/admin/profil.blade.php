@extends('panel', ['judul' => 'Ganti kata sandi'])
@section('isi')
<form class="form kartu" method="post" action="{{ route('admin.profil.sandi') }}" style="max-width:560px;margin-top:0" novalidate>@csrf
  <p class="muted" style="margin-top:0">Masuk sebagai <b>{{ $u->email }}</b></p>
  <div class="f"><label for="sandi_lama">Kata sandi lama</label><input id="sandi_lama" type="password" name="sandi_lama" autocomplete="current-password">@error('sandi_lama')<div class="galat">{{ $message }}</div>@enderror</div>
  <div class="dua"><div class="f"><label for="password">Kata sandi baru</label><input id="password" type="password" name="password" autocomplete="new-password">@error('password')<div class="galat">{{ $message }}</div>@enderror</div><div class="f"><label for="password_confirmation">Ulangi</label><input id="password_confirmation" type="password" name="password_confirmation" autocomplete="new-password"></div></div>
  <p class="kecil muted">Minimal 8 karakter, berisi huruf dan angka.</p>
  <button class="btn">Ganti kata sandi</button>
</form>
@endsection
