@php
  $k = \App\Support\Kelurahan::semua();
  $logo = $k['logo'] ? asset('uploads/' . $k['logo']) : asset('img/logo-surat.svg');
  $menu = [['beranda', 'Beranda'], ['layanan', 'Layanan'], ['cek-status', 'Cek status'], ['kabar', 'Kabar'], ['pengaduan', 'Pengaduan']];
  $u = auth()->user();
@endphp
<!doctype html>
<html lang="id">
<head>
<meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
<title>{{ isset($judul) ? $judul . ' - ' : '' }}Kelurahan {{ $k['nama'] }}</title>
<meta name="description" content="{{ $deskripsi ?? 'Layanan administrasi online Kelurahan ' . $k['nama'] . ': ajukan surat keterangan, cek status permohonan, dan kirim pengaduan.' }}">
<link rel="icon" href="{{ $logo }}">
<link rel="stylesheet" href="{{ asset('css/situs.css') }}">
</head>
<body>
<a class="sr" href="#isi">Lewati ke konten</a>
<div class="atas"><div class="wadah">
  <div class="{{ $heroBesar ?? false ? 'hero' : 'hero-kecil' }}" style="position:relative">
    <nav class="nav" aria-label="Menu utama">
      <a class="logo" href="{{ route('beranda') }}"><img src="{{ $logo }}" alt=""><span><b>Kelurahan {{ $k['nama'] }}</b><small>Kec. {{ $k['kecamatan'] }}, {{ $k['kota'] }}</small></span></a>
      <div class="menu" id="menu">
        @foreach ($menu as [$r, $l])<a href="{{ route($r) }}" class="{{ request()->routeIs($r, $r . '.*') ? 'on' : '' }}">{{ $l }}</a>@endforeach
      </div>
      @if ($k['telepon'])<div class="kontak"><span class="ikon">{!! ikon('telepon') !!}</span><span>Hubungi kami<b>{{ $k['telepon'] }}</b></span></div>@endif
      @if ($u)
        <a class="btn gelap" href="{{ $u->staf() ? route('admin.dasbor') : route('warga.beranda') }}"><span class="teks-btn">{{ $u->staf() ? 'Dasbor' : 'Akun saya' }}</span><span class="bulat">{!! ikon('panah') !!}</span></a>
      @else
        <a class="btn gelap" href="{{ route('masuk') }}"><span class="teks-btn">Ajukan surat</span><span class="bulat">{!! ikon('panah') !!}</span></a>
      @endif
      <button class="tombol-menu" type="button" aria-label="Buka menu" aria-controls="menu" onclick="document.getElementById('menu').classList.toggle('buka')">{!! ikon('menu') !!}</button>
    </nav>
    @yield('hero')
  </div>
</div></div>
<main id="isi">@yield('isi')</main>
<footer class="kaki">
  <div class="wadah">
    <div class="kisi">
      <div>
        <div class="logo-kaki"><img src="{{ $logo }}" alt=""><b style="font-size:17px">Kelurahan {{ $k['nama'] }}</b></div>
        <p style="margin:0 0 10px">{{ $k['alamat'] }}, Kec. {{ $k['kecamatan'] }}, {{ $k['kota'] }}, {{ $k['provinsi'] }} {{ $k['kode_pos'] }}</p>
        <p style="margin:0">Layanan tidak dipungut biaya. Laporkan pungutan liar lewat menu pengaduan.</p>
      </div>
      <div><h4>Layanan</h4><ul><li><a href="{{ route('layanan') }}">Jenis surat</a></li><li><a href="{{ route('cek-status') }}">Cek status</a></li><li><a href="{{ route('pengaduan') }}">Pengaduan</a></li><li><a href="{{ route('daftar') }}">Daftar akun warga</a></li></ul></div>
      <div><h4>Informasi</h4><ul><li><a href="{{ route('kabar') }}">Kabar kelurahan</a></li><li><a href="{{ route('beranda') }}#jam">Jam layanan</a></li><li><a href="{{ route('beranda') }}#faq">Tanya jawab</a></li><li><a href="{{ route('masuk') }}">Masuk petugas</a></li></ul></div>
      <div><h4>Kontak</h4><ul>
        @if ($k['telepon'])<li>{{ $k['telepon'] }}</li>@endif
        @if (\App\Support\Kelurahan::waLink())<li><a href="{{ \App\Support\Kelurahan::waLink() }}" target="_blank" rel="noopener">WhatsApp {{ $k['whatsapp'] }}</a></li>@endif
        @if ($k['email'])<li><a href="mailto:{{ $k['email'] }}">{{ $k['email'] }}</a></li>@endif
      </ul></div>
    </div>
    <div class="bawah"><span>&copy; {{ date('Y') }} Kelurahan {{ $k['nama'] }}</span><span>Pelayanan cepat, transparan, tanpa pungli</span></div>
  </div>
</footer>
@stack('skrip')
</body>
</html>
