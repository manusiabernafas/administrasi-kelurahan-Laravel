@extends('publik.layout', ['judul' => 'Pengaduan warga'])
@section('hero')<div class="judul"><span class="kicker">Pengaduan warga</span><h1>Sampaikan keluhan, <span class="serif">kami tindak lanjuti</span></h1><p>Jalan rusak, sampah menumpuk, lampu jalan mati, atau pelayanan yang kurang baik. Identitas pelapor dijaga kerahasiaannya.</p></div>@endsection
@section('isi')
<div class="wadah konten dua-kolom">
  <form class="form panel putih" method="post" action="{{ route('pengaduan.kirim') }}" enctype="multipart/form-data" novalidate>
    @csrf
    @if (session('kode'))<div class="alert ok">Pengaduan terkirim. Simpan kode berikut untuk melihat tanggapan:<div class="kode-besar">{{ session('kode') }}</div></div>@endif
    @if (session('ok'))<div class="alert ok">{{ session('ok') }}</div>@endif
    <div class="dua">
      <div class="f"><label for="nama">Nama</label><input id="nama" name="nama" value="{{ old('nama', auth()->user()->name ?? '') }}" maxlength="120" required>@error('nama')<div class="galat">{{ $message }}</div>@enderror</div>
      <div class="f"><label for="kontak">Nomor HP / email</label><input id="kontak" name="kontak" value="{{ old('kontak', auth()->user()->telepon ?? '') }}" maxlength="120" required>@error('kontak')<div class="galat">{{ $message }}</div>@enderror</div>
    </div>
    <div class="dua">
      <div class="f"><label for="kategori">Kategori</label><select id="kategori" name="kategori" required><option value="">Pilih kategori</option>@foreach (\App\Models\Pengaduan::KATEGORI as $kat)<option @selected(old('kategori') === $kat)>{{ $kat }}</option>@endforeach</select>@error('kategori')<div class="galat">{{ $message }}</div>@enderror</div>
      <div class="f"><label for="lokasi">Lokasi (opsional)</label><input id="lokasi" name="lokasi" value="{{ old('lokasi') }}" maxlength="200" placeholder="Contoh: depan Posyandu RW 03">@error('lokasi')<div class="galat">{{ $message }}</div>@enderror</div>
    </div>
    <div class="f"><label for="isi">Isi pengaduan</label><textarea id="isi" name="isi" maxlength="3000" required placeholder="Ceritakan apa yang terjadi, sejak kapan, dan dampaknya.">{{ old('isi') }}</textarea>@error('isi')<div class="galat">{{ $message }}</div>@enderror</div>
    <div class="f"><label for="lampiran">Foto pendukung (opsional)</label><input id="lampiran" type="file" name="lampiran" accept="image/jpeg,image/png,image/webp">@error('lampiran')<div class="galat">{{ $message }}</div>@enderror</div>
    <div class="hp" aria-hidden="true"><label for="situs">Jangan diisi</label><input id="situs" name="situs" tabindex="-1" autocomplete="off"></div>
    <button class="btn gelap">Kirim pengaduan <span class="bulat">{!! ikon('panah') !!}</span></button>
  </form>
  <aside class="panel"><h2>Setelah mengirim</h2><ol class="syarat"><li>Kamu mendapat kode pengaduan (ADU-XXXXXX).</li><li>Petugas mempelajari laporan dan meneruskannya ke pihak terkait.</li><li>Tanggapan bisa dilihat di menu Cek status memakai kode tersebut.</li></ol></aside>
</div>
@endsection
