@extends('publik.layout', ['judul' => 'Layanan'])
@section('hero')<div class="judul"><span class="kicker">Layanan administrasi</span><h1>Pilih surat yang <span class="serif">kamu butuhkan</span></h1><p>Semua layanan gratis. Klik salah satu untuk melihat syarat dan cara mengajukannya.</p></div>@endsection
@section('isi')
<div class="wadah konten"><div class="kisi-layanan">@foreach ($layanan as $l)@include('publik._kartu-layanan')@endforeach</div></div>
@endsection
