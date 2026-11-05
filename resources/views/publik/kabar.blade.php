@extends('publik.layout', ['judul' => 'Kabar kelurahan'])
@section('hero')<div class="judul"><span class="kicker">Kabar kelurahan</span><h1>Pengumuman & <span class="serif">kegiatan warga</span></h1><p>Informasi terbaru seputar layanan, kegiatan, dan program di lingkungan kelurahan.</p></div>@endsection
@section('isi')
<div class="wadah konten">
  <div class="filter"><a href="{{ route('kabar') }}" class="{{ $aktif ? '' : 'on' }}">Semua</a>@foreach ($kategori as $kat)<a href="{{ route('kabar', ['kategori' => $kat]) }}" class="{{ $aktif === $kat ? 'on' : '' }}">{{ $kat }}</a>@endforeach</div>
  @if ($daftar->isEmpty())<div class="kosong">Belum ada kabar.</div>@endif
  <div class="kisi-kabar">@foreach ($daftar as $x)@include('publik._kartu-kabar')@endforeach</div>
  @include('publik._pagin', ['p' => $daftar])
</div>
@endsection
