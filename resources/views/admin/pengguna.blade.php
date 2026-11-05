@extends('panel', ['judul' => 'Pengguna', 'sub' => 'Akun petugas kelurahan dan akun warga'])
@section('isi')
<div class="grid g3" style="align-items:start">
  <div class="kartu lebar-2" style="margin-top:0"><h2>{!! ikon('kunci') !!} Petugas & admin</h2>
    @foreach ($staf as $s)
      <form class="form" method="post" action="{{ route('admin.pengguna.update', $s) }}" style="display:grid;grid-template-columns:minmax(0,1.3fr) minmax(0,1.3fr) 130px auto auto;gap:8px;align-items:center;padding:10px 0;border-top:1px solid var(--garis)">
        @csrf @method('PUT')
        <input name="name" value="{{ $s->name }}" aria-label="Nama"><input name="email" value="{{ $s->email }}" aria-label="Email">
        <select name="peran" aria-label="Peran"><option value="admin" @selected($s->peran === 'admin')>Admin</option><option value="petugas" @selected($s->peran === 'petugas')>Petugas</option></select>
        <label class="cek kecil"><input type="checkbox" name="aktif" value="1" @checked($s->aktif)> Aktif</label>
        <span style="display:flex;gap:6px"><button class="btn garis kecil">Simpan</button></span>
      </form>
      <form method="post" action="{{ route('admin.pengguna.reset', $s) }}" data-yakin="Reset kata sandi {{ $s->name }}?" style="margin:-4px 0 6px">@csrf<button class="btn garis kecil">{!! ikon('kunci') !!} Reset sandi</button></form>
    @endforeach
    <p class="kecil muted">Admin bisa mengatur pengguna & pengaturan kelurahan. Petugas bisa memproses permohonan, data penduduk, kabar, dan pengaduan.</p>
  </div>
  <form class="form kartu" method="post" action="{{ route('admin.pengguna.store') }}" style="margin-top:0" novalidate>@csrf<h2>{!! ikon('plus') !!} Tambah petugas</h2>
    <div class="f"><label for="name">Nama</label><input id="name" name="name" value="{{ old('name') }}">@error('name')<div class="galat">{{ $message }}</div>@enderror</div>
    <div class="f"><label for="email">Email (untuk masuk)</label><input id="email" type="email" name="email" value="{{ old('email') }}">@error('email')<div class="galat">{{ $message }}</div>@enderror</div>
    <div class="f"><label for="peran">Peran</label><select id="peran" name="peran"><option value="petugas">Petugas</option><option value="admin">Admin</option></select></div>
    <p class="kecil muted">Kata sandi dibuat otomatis dan ditampilkan sekali setelah disimpan.</p>
    <button class="btn" style="width:100%">Buat akun</button></form>
</div>
<div class="kartu"><h2>{!! ikon('pengguna') !!} Akun warga</h2>
  <form class="cari" method="get" style="margin-bottom:12px"><input type="search" name="q" value="{{ request('q') }}" placeholder="Cari nama atau NIK"><button class="btn garis">Cari</button></form>
  <div class="tabel-wrap"><table class="tabel"><thead><tr><th>Nama</th><th class="hp-sembunyi">NIK</th><th class="hp-sembunyi">HP</th><th>Status</th><th></th></tr></thead><tbody>
    @forelse ($warga as $w)<tr><td><b>{{ $w->name }}</b></td><td class="hp-sembunyi kode">{{ $w->nik }}</td><td class="hp-sembunyi">{{ $w->telepon }}</td><td>{!! lencana($w->aktif ? 'Aktif' : 'Diblokir', $w->aktif ? 'hijau' : 'merah') !!}</td>
      <td class="aksi"><form method="post" action="{{ route('admin.pengguna.reset', $w) }}" data-yakin="Reset kata sandi {{ $w->name }}?">@csrf<button class="btn garis kecil">{!! ikon('kunci') !!}<span class="hp-sembunyi">Reset sandi</span></button></form>
        <form method="post" action="{{ route('admin.pengguna.update', $w) }}">@csrf @method('PUT')<input type="hidden" name="name" value="{{ $w->name }}"><input type="hidden" name="peran" value="warga">@unless ($w->aktif)<input type="hidden" name="aktif" value="1">@endunless<button class="btn {{ $w->aktif ? 'merah' : 'garis' }} kecil">{{ $w->aktif ? 'Blokir' : 'Aktifkan' }}</button></form></td></tr>
    @empty<tr><td colspan="5" class="muted">Belum ada warga yang mendaftar.</td></tr>@endforelse
  </tbody></table></div>@include('publik._pagin', ['p' => $warga])</div>
@endsection
