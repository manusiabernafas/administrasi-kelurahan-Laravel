@extends('panel', ['judul' => 'Beranda', 'sub' => 'Pantau semua permohonan surat kamu di sini'])
@section('aksi')<a class="btn lime" href="{{ route('warga.pilih') }}">{!! ikon('plus') !!} Ajukan surat</a>@endsection
@section('isi')
<div class="sambut"><div><h2>Halo, <span class="serif">{{ \Illuminate\Support\Str::title(mb_strtolower($penduduk->nama ?? auth()->user()->name)) }}</span></h2><p>NIK {{ auth()->user()->nik }} &middot; {{ $penduduk ? $penduduk->alamat_lengkap : '' }}</p></div><a class="btn lime" href="{{ route('warga.pilih') }}">{!! ikon('dokumen') !!} Buat permohonan baru</a></div>
@php $aktif = $daftar->whereIn('status', ['diajukan', 'diverifikasi', 'diproses'])->count(); @endphp
<div class="grid g3" style="margin-bottom:18px">
  <div class="stat sorot"><div class="ikon">{!! ikon('jam') !!}</div><b>{{ $aktif }}</b><span>sedang diproses</span></div>
  <div class="stat"><div class="ikon">{!! ikon('cek') !!}</div><b>{{ $daftar->where('status', 'selesai')->count() }}</b><span>surat selesai</span></div>
  <div class="stat"><div class="ikon">{!! ikon('dokumen') !!}</div><b>{{ $daftar->count() }}</b><span>total permohonan</span></div>
</div>
<div class="kartu">
  <h2>Permohonan saya</h2>
  @if ($daftar->isEmpty())
    <div class="kosong">{!! ikon('kotak-masuk') !!}<p>Belum ada permohonan. Klik <b>Ajukan surat</b> untuk memulai.</p></div>
  @else
  <div class="tabel-wrap"><table class="tabel">
    <thead><tr><th>Surat</th><th class="hp-sembunyi">Kode lacak</th><th class="hp-sembunyi">Tanggal</th><th>Status</th><th></th></tr></thead>
    <tbody>@foreach ($daftar as $p)
      <tr><td><b>{{ $p->layanan->nama }}</b><div class="kecil muted">{{ \Illuminate\Support\Str::limit($p->keperluan, 50) }}</div></td><td class="hp-sembunyi kode">{{ $p->kode }}</td><td class="hp-sembunyi kecil">{{ tgl($p->created_at) }}</td><td>{!! lencana($p->label_status, $p->warna_status) !!}</td>
        <td class="aksi">@if ($p->status === 'selesai')<a class="btn lime kecil" href="{{ route('berkas.surat', $p->id) }}">{!! ikon('unduh') !!}<span class="hp-sembunyi">Unduh</span></a>@endif <a class="btn garis kecil" href="{{ route('warga.detail', $p->id) }}">Detail</a></td></tr>
    @endforeach</tbody>
  </table></div>
  @endif
</div>
<div class="kartu"><h2>Layanan populer <a class="aksi-h" href="{{ route('warga.pilih') }}">Semua layanan</a></h2><div class="pilih-layanan">@foreach ($layanan as $l)<a href="{{ route('warga.ajukan', strtolower($l->kode)) }}"><span class="ikon">{!! ikon($l->ikon) !!}</span><b>{{ $l->nama }}</b><span>{{ $l->estimasi_hari ? $l->estimasi_hari . ' hari kerja' : 'Hari itu juga' }}</span></a>@endforeach</div></div>
@endsection
