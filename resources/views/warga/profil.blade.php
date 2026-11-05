@extends('panel', ['judul' => 'Profil & kata sandi'])
@section('isi')
<div class="grid g2" style="align-items:start">
  <div class="kartu"><h2>Data kependudukan</h2>
    @if ($penduduk)<dl class="dl"><dt>Nama</dt><dd>{{ $penduduk->nama }}</dd><dt>NIK</dt><dd>{{ $penduduk->nik }}</dd><dt>No. KK</dt><dd>{{ $penduduk->no_kk }}</dd><dt>Tempat, tgl lahir</dt><dd>{{ $penduduk->ttl }}</dd><dt>Jenis kelamin</dt><dd>{{ $penduduk->jenis_kelamin === 'L' ? 'Laki-laki' : 'Perempuan' }}</dd><dt>Agama</dt><dd>{{ $penduduk->agama }}</dd><dt>Pekerjaan</dt><dd>{{ $penduduk->pekerjaan ?: '-' }}</dd><dt>Alamat</dt><dd>{{ $penduduk->alamat_lengkap }}</dd></dl>
    <p class="kecil muted" style="margin:14px 0 0">Perubahan data kependudukan dilakukan oleh petugas kelurahan.</p>@endif
  </div>
  <div>
    <form class="form kartu" method="post" action="{{ route('warga.profil.simpan') }}" novalidate>@csrf<h2>Kontak</h2>
      <div class="f"><label for="telepon">Nomor HP / WhatsApp</label><input id="telepon" name="telepon" value="{{ old('telepon', $u->telepon) }}">@error('telepon')<div class="galat">{{ $message }}</div>@enderror</div>
      <div class="f"><label for="email">Email (opsional)</label><input id="email" type="email" name="email" value="{{ old('email', $u->email) }}">@error('email')<div class="galat">{{ $message }}</div>@enderror</div>
      <button class="btn">Simpan</button></form>
    <form class="form kartu" method="post" action="{{ route('warga.sandi') }}" novalidate>@csrf<h2>Ganti kata sandi</h2>
      <div class="f"><label for="sandi_lama">Kata sandi lama</label><input id="sandi_lama" type="password" name="sandi_lama" autocomplete="current-password">@error('sandi_lama')<div class="galat">{{ $message }}</div>@enderror</div>
      <div class="dua"><div class="f"><label for="password">Kata sandi baru</label><input id="password" type="password" name="password" autocomplete="new-password">@error('password')<div class="galat">{{ $message }}</div>@enderror</div><div class="f"><label for="password_confirmation">Ulangi</label><input id="password_confirmation" type="password" name="password_confirmation" autocomplete="new-password"></div></div>
      <button class="btn garis">Ganti kata sandi</button></form>
  </div>
</div>
@endsection
