@extends('publik.layout', ['judul' => $k->judul, 'deskripsi' => $k->ringkas])
@section('hero')<div class="judul"><a href="{{ route('kabar') }}" class="kicker" style="text-decoration:none">{{ $k->kategori }}</a><h1>{{ $k->judul }}</h1><p>{{ tgl($k->terbit_at) }} &middot; dibaca {{ $k->dilihat }} kali</p></div>@endsection
@section('isi')
<div class="wadah konten" style="max-width:820px">
  @if ($k->gambar)<img src="{{ asset('uploads/' . $k->gambar) }}" alt="" style="border-radius:22px;width:100%;margin-bottom:30px">@endif
  <div class="prosa">{!! nl2br(e($k->isi), false) !!}</div>
</div>
@if ($lain->isNotEmpty())<section class="seksi" style="padding-bottom:0"><div class="wadah"><div class="kepala-seksi"><div><span class="kicker">Kabar lainnya</span></div></div><div class="kisi-kabar">@foreach ($lain as $x)@include('publik._kartu-kabar')@endforeach</div></div></section>@endif
@endsection
