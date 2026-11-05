@php $baru = ! $p->exists; @endphp
@extends('panel', ['judul' => $baru ? 'Tambah penduduk' : 'Ubah data ' . $p->nama])
@section('aksi')<a class="btn garis" href="{{ $baru ? route('admin.penduduk.index') : route('admin.penduduk.show', $p) }}">{!! ikon('kiri') !!} Kembali</a>@endsection
@section('isi')
@php $v = fn ($k) => old($k, $k === 'tanggal_lahir' ? $p->tanggal_lahir?->format('Y-m-d') : $p->$k); $C = \App\Http\Controllers\Admin\PendudukController::class; @endphp
<form class="form kartu" method="post" action="{{ $baru ? route('admin.penduduk.store') : route('admin.penduduk.update', $p) }}" style="max-width:920px" novalidate>
  @csrf @unless ($baru) @method('PUT') @endunless
  <h2>Identitas</h2>
  <div class="dua">
    <div class="f"><label for="nik">NIK</label><input id="nik" name="nik" value="{{ $v('nik') }}" inputmode="numeric" maxlength="16">@error('nik')<div class="galat">{{ $message }}</div>@enderror</div>
    <div class="f"><label for="no_kk">Nomor KK</label><input id="no_kk" name="no_kk" value="{{ $v('no_kk') }}" inputmode="numeric" maxlength="16">@error('no_kk')<div class="galat">{{ $message }}</div>@enderror</div>
  </div>
  <div class="f"><label for="nama">Nama lengkap (sesuai KTP)</label><input id="nama" name="nama" value="{{ $v('nama') }}">@error('nama')<div class="galat">{{ $message }}</div>@enderror</div>
  <div class="tiga">
    <div class="f"><label for="tempat_lahir">Tempat lahir</label><input id="tempat_lahir" name="tempat_lahir" value="{{ $v('tempat_lahir') }}">@error('tempat_lahir')<div class="galat">{{ $message }}</div>@enderror</div>
    <div class="f"><label for="tanggal_lahir">Tanggal lahir</label><input id="tanggal_lahir" type="date" name="tanggal_lahir" value="{{ $v('tanggal_lahir') }}">@error('tanggal_lahir')<div class="galat">{{ $message }}</div>@enderror</div>
    <div class="f"><label for="jenis_kelamin">Jenis kelamin</label><select id="jenis_kelamin" name="jenis_kelamin"><option value="">Pilih</option><option value="L" @selected($v('jenis_kelamin') === 'L')>Laki-laki</option><option value="P" @selected($v('jenis_kelamin') === 'P')>Perempuan</option></select>@error('jenis_kelamin')<div class="galat">{{ $message }}</div>@enderror</div>
  </div>
  <div class="tiga">
    <div class="f"><label for="agama">Agama</label><select id="agama" name="agama"><option value="">Pilih</option>@foreach ($C::AGAMA as $x)<option @selected($v('agama') === $x)>{{ $x }}</option>@endforeach</select>@error('agama')<div class="galat">{{ $message }}</div>@enderror</div>
    <div class="f"><label for="status_perkawinan">Status perkawinan</label><select id="status_perkawinan" name="status_perkawinan"><option value="">Pilih</option>@foreach ($C::KAWIN as $x)<option @selected($v('status_perkawinan') === $x)>{{ $x }}</option>@endforeach</select>@error('status_perkawinan')<div class="galat">{{ $message }}</div>@enderror</div>
    <div class="f"><label for="pendidikan">Pendidikan</label><select id="pendidikan" name="pendidikan"><option value="">-</option>@foreach ($C::PENDIDIKAN as $x)<option @selected($v('pendidikan') === $x)>{{ $x }}</option>@endforeach</select>@error('pendidikan')<div class="galat">{{ $message }}</div>@enderror</div>
  </div>
  <div class="dua">
    <div class="f"><label for="pekerjaan">Pekerjaan</label><input id="pekerjaan" name="pekerjaan" value="{{ $v('pekerjaan') }}">@error('pekerjaan')<div class="galat">{{ $message }}</div>@enderror</div>
    <div class="f"><label for="kewarganegaraan">Kewarganegaraan</label><select id="kewarganegaraan" name="kewarganegaraan">@foreach (['WNI', 'WNA'] as $x)<option @selected($v('kewarganegaraan') === $x)>{{ $x }}</option>@endforeach</select></div>
  </div>
  <h2 style="margin-top:8px">Alamat</h2>
  <div class="f"><label for="alamat">Jalan / blok / nomor rumah</label><input id="alamat" name="alamat" value="{{ $v('alamat') }}">@error('alamat')<div class="galat">{{ $message }}</div>@enderror</div>
  <div class="tiga">
    <div class="f"><label for="rt">RT</label><input id="rt" name="rt" value="{{ $v('rt') }}" inputmode="numeric" maxlength="3">@error('rt')<div class="galat">{{ $message }}</div>@enderror</div>
    <div class="f"><label for="rw">RW</label><input id="rw" name="rw" value="{{ $v('rw') }}" inputmode="numeric" maxlength="3">@error('rw')<div class="galat">{{ $message }}</div>@enderror</div>
    <div class="f"><label for="status">Status</label><select id="status" name="status">@foreach (['aktif' => 'Aktif', 'pindah' => 'Pindah', 'meninggal' => 'Meninggal'] as $x => $l)<option value="{{ $x }}" @selected($v('status') === $x)>{{ $l }}</option>@endforeach</select></div>
  </div>
  <div style="display:flex;gap:10px;flex-wrap:wrap"><button class="btn">{!! ikon('cek') !!} Simpan</button></div>
</form>
@unless ($baru)
<form method="post" action="{{ route('admin.penduduk.destroy', $p) }}" data-yakin="Hapus data penduduk ini?" style="margin-top:14px">@csrf @method('DELETE')<button class="btn merah kecil">{!! ikon('hapus') !!} Hapus data</button></form>
@endunless
@endsection
