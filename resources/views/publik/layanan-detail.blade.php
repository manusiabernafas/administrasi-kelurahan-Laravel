@extends('publik.layout', ['judul' => $l->nama, 'deskripsi' => $l->ringkas])
@section('hero')<div class="judul"><a href="{{ route('layanan') }}" class="kicker" style="text-decoration:none">Semua layanan</a><h1>{{ $l->nama }}</h1><p>{{ $l->ringkas }}</p></div>@endsection
@section('isi')
<div class="wadah konten dua-kolom">
  <div>
    <div class="panel"><h2>Persyaratan</h2><ol class="syarat">@foreach ($l->syarat as $s)<li>{{ $s }}</li>@endforeach</ol></div>
    @if ($l->isian)
      <div class="panel putih" style="margin-top:18px"><h2>Data yang perlu disiapkan</h2><ul style="margin:0;padding-left:20px">@foreach ($l->isian as $f)<li>{{ $f['label'] }}{{ $f['wajib'] ? '' : ' (opsional)' }}</li>@endforeach<li>Keperluan pembuatan surat</li></ul></div>
    @endif
  </div>
  <aside class="panel" style="background:var(--tua);color:#fff">
    <span class="lencana hijau">{{ $l->estimasi_hari ? 'Selesai ' . $l->estimasi_hari . ' hari kerja' : 'Selesai hari itu juga' }}</span>
    <h2 style="font-size:26px;margin:16px 0 8px">Ajukan secara online</h2>
    <p style="opacity:.85;margin:0 0 22px">Masuk dengan akun warga, isi formulir, dan unggah foto berkas. Atau datang ke loket membawa berkas asli.</p>
    <a class="btn" href="{{ route('warga.ajukan', strtolower($l->kode)) }}">Ajukan sekarang <span class="bulat">{!! ikon('panah') !!}</span></a>
    <p class="kecil" style="opacity:.75;margin:18px 0 0">Belum punya akun? <a href="{{ route('daftar') }}" style="color:var(--lime)">Daftar dengan NIK</a></p>
  </aside>
</div>
<section class="seksi" style="padding-bottom:0"><div class="wadah"><div class="kepala-seksi"><div><span class="kicker">Layanan lain</span><h2>Mungkin kamu juga <span class="serif">perlu</span></h2></div></div><div class="kisi-layanan" style="grid-template-columns:repeat(auto-fit,minmax(240px,1fr))">@foreach ($lain as $l)@include('publik._kartu-layanan')@endforeach</div></div></section>
@endsection
