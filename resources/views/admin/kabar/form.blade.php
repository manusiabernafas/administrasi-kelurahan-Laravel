@php $baru = ! $k->exists; @endphp
@extends('panel', ['judul' => $baru ? 'Tulis kabar' : 'Ubah kabar'])
@section('aksi')<a class="btn garis" href="{{ route('admin.kabar.index') }}">{!! ikon('kiri') !!} Kembali</a>@endsection
@section('isi')
@php $v = fn ($x) => old($x, $k->$x); @endphp
<form class="form" method="post" action="{{ $baru ? route('admin.kabar.store') : route('admin.kabar.update', $k) }}" enctype="multipart/form-data" novalidate>
  @csrf @unless ($baru) @method('PUT') @endunless
  <div class="grid g3" style="align-items:start">
    <div class="kartu lebar-2" style="margin-top:0">
      <div class="f"><label for="judul">Judul</label><input id="judul" name="judul" value="{{ $v('judul') }}">@error('judul')<div class="galat">{{ $message }}</div>@enderror</div>
      <div class="f"><label for="ringkas">Ringkasan (opsional)</label><input id="ringkas" name="ringkas" value="{{ $v('ringkas') }}" maxlength="300">@error('ringkas')<div class="galat">{{ $message }}</div>@enderror</div>
      <div class="f"><label for="isi">Isi</label><textarea id="isi" name="isi" style="min-height:320px">{{ $v('isi') }}</textarea><div class="hint">Pisahkan paragraf dengan baris kosong.</div>@error('isi')<div class="galat">{{ $message }}</div>@enderror</div>
    </div>
    <div class="kartu" style="margin-top:0">
      <div class="f"><label for="kategori">Kategori</label><select id="kategori" name="kategori">@foreach (\App\Http\Controllers\Admin\PengumumanController::KATEGORI as $x)<option @selected($v('kategori') === $x)>{{ $x }}</option>@endforeach</select></div>
      <div class="f"><label for="terbit_at">Tanggal tayang</label><input id="terbit_at" type="datetime-local" name="terbit_at" value="{{ old('terbit_at', $k->terbit_at?->format('Y-m-d\TH:i')) }}"><div class="hint">Kosongkan untuk tayang sekarang.</div>@error('terbit_at')<div class="galat">{{ $message }}</div>@enderror</div>
      <div class="f"><label for="gambar">Gambar (opsional)</label>@if ($k->gambar)<img src="{{ asset('uploads/' . $k->gambar) }}" alt="" style="border-radius:12px;margin-bottom:8px">@endif<input id="gambar" type="file" name="gambar" accept="image/jpeg,image/png,image/webp">@error('gambar')<div class="galat">{{ $message }}</div>@enderror</div>
      <div class="f"><label class="cek"><input type="checkbox" name="terbit" value="1" @checked(old('terbit', $k->terbit))> Terbitkan</label></div>
      <button class="btn" style="width:100%">{!! ikon('cek') !!} Simpan</button>
    </div>
  </div>
</form>
@endsection
