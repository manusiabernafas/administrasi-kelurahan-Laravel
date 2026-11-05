@extends('panel', ['judul' => 'Ajukan surat', 'sub' => 'Langkah 1 dari 2: pilih jenis surat'])
@section('isi')
<div class="kartu"><div class="pilih-layanan">@foreach ($layanan as $l)<a href="{{ route('warga.ajukan', strtolower($l->kode)) }}"><span class="ikon">{!! ikon($l->ikon) !!}</span><b>{{ $l->nama }}</b><span>{{ $l->ringkas }}</span><span class="lencana hijau" style="align-self:flex-start">{{ $l->estimasi_hari ? $l->estimasi_hari . ' hari kerja' : 'Hari itu juga' }}</span></a>@endforeach</div></div>
@endsection
