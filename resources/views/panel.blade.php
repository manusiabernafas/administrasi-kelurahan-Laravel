@php
  $k = \App\Support\Kelurahan::semua();
  $logo = $k['logo'] ? asset('uploads/' . $k['logo']) : asset('img/logo-surat.svg');
  $u = auth()->user();
  if ($u->staf()) {
    $baru = \App\Models\Permohonan::where('status', 'diajukan')->count();
    $aduan = \App\Models\Pengaduan::where('status', 'baru')->count();
    $menu = [
      'Utama' => [['admin.dasbor', 'Dasbor', 'grid', null, 'admin.dasbor'], ['admin.permohonan.index', 'Permohonan surat', 'kotak-masuk', $baru, 'admin.permohonan.*'], ['admin.permohonan.create', 'Layanan loket', 'plus', null, null], ['admin.pengaduan.index', 'Pengaduan warga', 'pesan', $aduan, 'admin.pengaduan.*']],
      'Data' => [['admin.penduduk.index', 'Data penduduk', 'pengguna', null, 'admin.penduduk.*'], ['admin.layanan.index', 'Jenis layanan', 'dokumen', null, 'admin.layanan.*'], ['admin.kabar.index', 'Kabar & pengumuman', 'toa', null, 'admin.kabar.*'], ['admin.laporan', 'Laporan', 'grafik', null, 'admin.laporan*']],
    ];
    if ($u->peran === 'admin') $menu['Sistem'] = [['admin.pengguna.index', 'Pengguna', 'kunci', null, 'admin.pengguna.*'], ['admin.pengaturan', 'Pengaturan kelurahan', 'atur', null, 'admin.pengaturan*']];
    $menu['Akun'] = [['admin.profil', 'Ganti kata sandi', 'user', null, 'admin.profil*']];
  } else {
    $menu = ['Menu' => [['warga.beranda', 'Beranda', 'rumah', null, 'warga.beranda'], ['warga.pilih', 'Ajukan surat', 'plus', null, 'warga.pilih'], ['warga.profil', 'Profil & kata sandi', 'user', null, 'warga.profil'], ['pengaduan', 'Kirim pengaduan', 'pesan', null, null]]];
  }
@endphp
<!doctype html>
<html lang="id">
<head>
<meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><meta name="robots" content="noindex">
<title>{{ $judul ?? 'Dasbor' }} - Kelurahan {{ $k['nama'] }}</title>
<link rel="icon" href="{{ $logo }}">
<link rel="stylesheet" href="{{ asset('css/panel.css') }}">
</head>
<body>
<div class="app">
  <aside class="side" id="side">
    <a class="merek" href="{{ route('beranda') }}"><img src="{{ $logo }}" alt=""><span><b>Kelurahan {{ $k['nama'] }}</b><small>{{ $u->staf() ? 'Panel petugas' : 'Akun warga' }}</small></span></a>
    @foreach ($menu as $grup => $isi)
      <div class="grup">{{ $grup }}</div>
      @foreach ($isi as [$r, $l, $ic, $jml, $pola])
        <a class="nav {{ $pola && request()->routeIs($pola) ? 'on' : '' }}" href="{{ route($r) }}">{!! ikon($ic) !!} {{ $l }} @if ($jml)<span class="jml">{{ $jml }}</span>@endif</a>
      @endforeach
    @endforeach
    <div class="bawah">
      <div class="siapa"><span class="av">{{ mb_substr($u->name, 0, 1) }}</span><span><b>{{ $u->name }}</b>{{ ['admin' => 'Admin', 'petugas' => 'Petugas', 'warga' => 'Warga'][$u->peran] }}</span></div>
      <a class="nav" href="{{ route('beranda') }}" target="_blank" rel="noopener">{!! ikon('rumah') !!} Lihat situs</a>
      <form method="post" action="{{ route('keluar') }}">@csrf<button class="nav" type="submit">{!! ikon('keluar') !!} Keluar</button></form>
    </div>
  </aside>
  <main class="utama">
    <div class="atas">
      <button class="tombol-menu" type="button" aria-label="Buka menu" aria-controls="side" onclick="document.getElementById('side').classList.toggle('buka')">{!! ikon('menu') !!}</button>
      <h1>{{ $judul ?? 'Dasbor' }}@isset($sub)<span class="sub">{{ $sub }}</span>@endisset</h1>
      @yield('aksi')
    </div>
    @if (session('ok'))<div class="alert ok" role="status">{{ session('ok') }}</div>@endif
    @if (session('error'))<div class="alert err" role="alert">{{ session('error') }}</div>@endif
    @if (session('sandi_baru'))<div class="alert info">Kata sandi untuk <b>{{ session('sandi_baru')[0] }}</b>: <code>{{ session('sandi_baru')[1] }}</code>. Berikan ke pemilik akun sekarang, sandi ini hanya ditampilkan sekali.</div>@endif
    @if ($errors->any())<div class="alert err" role="alert">Ada isian yang perlu diperbaiki. Lihat tulisan merah di bawah.</div>@endif
    @yield('isi')
  </main>
</div>
<script>
document.addEventListener('click', e => { const s = document.getElementById('side'); if (s.classList.contains('buka') && !s.contains(e.target) && !e.target.closest('.tombol-menu')) s.classList.remove('buka'); });
document.querySelectorAll('form[data-yakin]').forEach(f => f.addEventListener('submit', e => { if (!confirm(f.dataset.yakin)) e.preventDefault(); }));
</script>
@stack('skrip')
</body>
</html>
