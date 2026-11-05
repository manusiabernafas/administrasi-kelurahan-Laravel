@extends('panel', ['judul' => 'Permohonan surat', 'sub' => 'Klik baris untuk memeriksa dan memproses permohonan'])
@section('aksi')<a class="btn lime" href="{{ route('admin.permohonan.create') }}">{!! ikon('plus') !!} Layanan loket</a>@endsection
@section('isi')
@php $total = $hitung->sum(); $aktif = ($hitung['diajukan'] ?? 0) + ($hitung['diverifikasi'] ?? 0) + ($hitung['diproses'] ?? 0); @endphp
<div class="filter">
  <a class="chip {{ ! request('status') ? 'on' : '' }}" href="{{ route('admin.permohonan.index', request()->except('status', 'page')) }}">Semua<span>{{ $total }}</span></a>
  <a class="chip {{ request('status') === 'aktif' ? 'on' : '' }}" href="{{ route('admin.permohonan.index', ['status' => 'aktif'] + request()->except('status', 'page')) }}">Belum selesai<span>{{ $aktif }}</span></a>
  @foreach (\App\Models\Permohonan::STATUS as $s => [$l])<a class="chip {{ request('status') === $s ? 'on' : '' }}" href="{{ route('admin.permohonan.index', ['status' => $s] + request()->except('status', 'page')) }}">{{ $l }}<span>{{ $hitung[$s] ?? 0 }}</span></a>@endforeach
</div>
<div class="kartu">
  <form class="cari" method="get" style="margin-bottom:12px">@if (request('status'))<input type="hidden" name="status" value="{{ request('status') }}">@endif
    <input type="search" name="q" value="{{ request('q') }}" placeholder="Cari nama, NIK, kode, nomor surat" aria-label="Cari">
    <select name="layanan" aria-label="Jenis surat"><option value="">Semua jenis surat</option>@foreach ($layanan as $l)<option value="{{ $l->id }}" @selected(request('layanan') == $l->id)>{{ $l->nama }}</option>@endforeach</select>
    <button class="btn garis">{!! ikon('cari') !!} Cari</button></form>
  @if ($daftar->isEmpty())<div class="kosong">{!! ikon('kotak-masuk') !!}<p>Tidak ada permohonan.</p></div>@else
  <div class="tabel-wrap"><table class="tabel"><thead><tr><th>Pemohon</th><th>Jenis surat</th><th class="hp-sembunyi">Kode</th><th class="hp-sembunyi">Masuk</th><th>Status</th><th></th></tr></thead><tbody>
    @foreach ($daftar as $p)
      <tr><td><b>{{ $p->penduduk->nama }}</b><div class="kecil muted">{{ $p->penduduk->nik }}</div></td><td>{{ $p->layanan->nama }}<div class="kecil muted">{{ $p->saluran === 'loket' ? 'Loket' : 'Online' }}</div></td><td class="hp-sembunyi kode">{{ $p->kode }}</td><td class="hp-sembunyi kecil">{{ tgl($p->created_at, true) }}</td><td>{!! lencana($p->label_status, $p->warna_status) !!}</td><td class="aksi"><a class="btn {{ in_array($p->status, ['diajukan', 'diverifikasi', 'diproses']) ? '' : 'garis' }} kecil" href="{{ route('admin.permohonan.show', $p) }}">{{ in_array($p->status, ['diajukan', 'diverifikasi', 'diproses']) ? 'Proses' : 'Lihat' }}</a></td></tr>
    @endforeach
  </tbody></table></div>
  @include('publik._pagin', ['p' => $daftar])
  @endif
</div>
@endsection
