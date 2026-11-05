@extends('panel', ['judul' => 'Pengaturan kelurahan', 'sub' => 'Dipakai di situs dan kop surat'])
@section('isi')
@php $v = fn ($x) => old($x, $k[$x]); @endphp
<form class="form" method="post" action="{{ route('admin.pengaturan.simpan') }}" enctype="multipart/form-data" novalidate>
  @csrf
  <div class="grid g2" style="align-items:start">
    <div>
      <div class="kartu" style="margin-top:0"><h2>{!! ikon('rumah') !!} Identitas wilayah</h2>
        <div class="dua"><div class="f"><label for="nama">Nama kelurahan</label><input id="nama" name="nama" value="{{ $v('nama') }}">@error('nama')<div class="galat">{{ $message }}</div>@enderror</div><div class="f"><label for="kecamatan">Kecamatan</label><input id="kecamatan" name="kecamatan" value="{{ $v('kecamatan') }}">@error('kecamatan')<div class="galat">{{ $message }}</div>@enderror</div></div>
        <div class="dua"><div class="f"><label for="kota">Kota / kabupaten</label><input id="kota" name="kota" value="{{ $v('kota') }}">@error('kota')<div class="galat">{{ $message }}</div>@enderror</div><div class="f"><label for="provinsi">Provinsi</label><input id="provinsi" name="provinsi" value="{{ $v('provinsi') }}">@error('provinsi')<div class="galat">{{ $message }}</div>@enderror</div></div>
        <div class="dua"><div class="f"><label for="alamat">Alamat kantor</label><input id="alamat" name="alamat" value="{{ $v('alamat') }}">@error('alamat')<div class="galat">{{ $message }}</div>@enderror</div><div class="f"><label for="kode_pos">Kode pos</label><input id="kode_pos" name="kode_pos" value="{{ $v('kode_pos') }}">@error('kode_pos')<div class="galat">{{ $message }}</div>@enderror</div></div>
        <div class="tiga"><div class="f"><label for="telepon">Telepon</label><input id="telepon" name="telepon" value="{{ $v('telepon') }}"></div><div class="f"><label for="whatsapp">WhatsApp</label><input id="whatsapp" name="whatsapp" value="{{ $v('whatsapp') }}">@error('whatsapp')<div class="galat">{{ $message }}</div>@enderror</div><div class="f"><label for="email">Email</label><input id="email" name="email" value="{{ $v('email') }}">@error('email')<div class="galat">{{ $message }}</div>@enderror</div></div>
        <div class="f"><label for="jam_layanan">Jam layanan (satu baris satu hari)</label><textarea id="jam_layanan" name="jam_layanan" style="min-height:90px">{{ $v('jam_layanan') }}</textarea>@error('jam_layanan')<div class="galat">{{ $message }}</div>@enderror</div>
        <div class="f"><label for="peta">Tautan embed Google Maps (opsional)</label><input id="peta" name="peta" value="{{ $v('peta') }}" placeholder="https://www.google.com/maps/embed?pb=..."><div class="hint">Google Maps &gt; Bagikan &gt; Sematkan peta, salin isi <code>src="..."</code>.</div>@error('peta')<div class="galat">{{ $message }}</div>@enderror</div>
      </div>
    </div>
    <div>
      <div class="kartu" style="margin-top:0"><h2>{!! ikon('dokumen') !!} Kop & tanda tangan surat</h2>
        <div class="f"><label for="logo">Logo pemerintah daerah (PNG/JPG)</label>@if ($k['logo'])<img src="{{ asset('uploads/' . $k['logo']) }}" alt="" style="height:70px;margin-bottom:8px">@endif<input id="logo" type="file" name="logo" accept="image/png,image/jpeg">@error('logo')<div class="galat">{{ $message }}</div>@enderror</div>
        <div class="dua"><div class="f"><label for="nama_lurah">Nama lurah</label><input id="nama_lurah" name="nama_lurah" value="{{ $v('nama_lurah') }}">@error('nama_lurah')<div class="galat">{{ $message }}</div>@enderror</div><div class="f"><label for="nip_lurah">NIP lurah</label><input id="nip_lurah" name="nip_lurah" value="{{ $v('nip_lurah') }}"></div></div>
        <div class="f"><label for="kode_surat">Kode kelurahan di nomor surat</label><input id="kode_surat" name="kode_surat" value="{{ $v('kode_surat') }}"><div class="hint">Contoh hasil: 012/SKD/{{ $k['kode_surat'] }}/X/{{ date('Y') }}</div>@error('kode_surat')<div class="galat">{{ $message }}</div>@enderror</div>
      </div>
      <div class="kartu"><h2>{!! ikon('grid') !!} Teks halaman depan</h2>
        <div class="f"><label for="tagline">Tagline</label><input id="tagline" name="tagline" value="{{ $v('tagline') }}">@error('tagline')<div class="galat">{{ $message }}</div>@enderror</div>
        <div class="f"><label for="sambutan">Sambutan / tentang kelurahan</label><textarea id="sambutan" name="sambutan">{{ $v('sambutan') }}</textarea>@error('sambutan')<div class="galat">{{ $message }}</div>@enderror</div>
        <button class="btn" style="width:100%">{!! ikon('cek') !!} Simpan pengaturan</button>
      </div>
    </div>
  </div>
</form>
@endsection
