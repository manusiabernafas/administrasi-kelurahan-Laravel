@extends('panel', ['judul' => 'Dasbor', 'sub' => tgl(now()) . ' - ringkasan pelayanan hari ini'])
@section('aksi')<a class="btn lime" href="{{ route('admin.permohonan.create') }}">{!! ikon('plus') !!} Layanan loket</a>@endsection
@section('isi')
@php $aktif = ($per['diajukan'] ?? 0) + ($per['diverifikasi'] ?? 0) + ($per['diproses'] ?? 0); $maks = max(1, $grafik->max('n')); @endphp
<div class="grid g4" style="margin-bottom:18px">
  <a class="stat sorot" href="{{ route('admin.permohonan.index', ['status' => 'diajukan']) }}"><div class="ikon">{!! ikon('kotak-masuk') !!}</div><b>{{ $per['diajukan'] ?? 0 }}</b><span>permohonan baru, perlu diperiksa</span></a>
  <a class="stat" href="{{ route('admin.permohonan.index', ['status' => 'aktif']) }}"><div class="ikon">{!! ikon('jam') !!}</div><b>{{ $aktif }}</b><span>belum selesai</span></a>
  <div class="stat"><div class="ikon">{!! ikon('cek') !!}</div><b>{{ $selesaiHariIni }}</b><span>surat terbit hari ini ({{ $masukHariIni }} masuk)</span></div>
  <a class="stat" href="{{ route('admin.pengaduan.index', ['status' => 'baru']) }}"><div class="ikon">{!! ikon('pesan') !!}</div><b>{{ $pengaduanBaru }}</b><span>pengaduan belum ditanggapi</span></a>
</div>
<div class="grid g3" style="align-items:start">
  <div class="kartu lebar-2" style="margin-top:0">
    <h2>{!! ikon('kotak-masuk') !!} Antrean yang perlu ditangani <a class="aksi-h" href="{{ route('admin.permohonan.index', ['status' => 'aktif']) }}">Lihat semua</a></h2>
    @if ($menunggu->isEmpty())<div class="kosong">{!! ikon('senyum') !!}<p>Semua permohonan sudah tertangani.</p></div>@else
    <div class="tabel-wrap"><table class="tabel"><thead><tr><th>Pemohon</th><th>Surat</th><th class="hp-sembunyi">Masuk</th><th>Status</th></tr></thead><tbody>
      @foreach ($menunggu as $p)<tr onclick="location.href='{{ route('admin.permohonan.show', $p) }}'" style="cursor:pointer"><td><b>{{ $p->penduduk->nama }}</b><div class="kecil muted kode">{{ $p->kode }}</div></td><td>{{ $p->layanan->nama }}</td><td class="hp-sembunyi kecil">{{ $p->created_at->diffForHumans() }}</td><td>{!! lencana($p->label_status, $p->warna_status) !!}</td></tr>@endforeach
    </tbody></table></div>@endif
  </div>
  <div class="kartu" style="margin-top:0"><h2>{!! ikon('grafik') !!} Terbanyak bulan ini</h2>
    @forelse ($terlaris as $t)<div style="margin-bottom:12px"><div style="display:flex;justify-content:space-between;font-size:14px;font-weight:700"><span>{{ $t->nama }}</span><span>{{ $t->n }}</span></div><div style="height:8px;background:var(--latar);border-radius:8px;margin-top:5px"><div style="height:100%;width:{{ round($t->n / max(1, $terlaris->max('n')) * 100) }}%;background:var(--daun);border-radius:8px"></div></div></div>@empty<p class="muted">Belum ada permohonan bulan ini.</p>@endforelse
  </div>
</div>
<div class="grid g3" style="margin-top:18px;align-items:start">
  <div class="kartu lebar-2" style="margin-top:0"><h2>{!! ikon('kalender') !!} Permohonan masuk 14 hari terakhir</h2>
    <div class="batang">@foreach ($grafik as $g)<div title="{{ tgl($g['t']) }}: {{ $g['n'] }}"><b>{{ $g['n'] ?: '' }}</b><i style="height:{{ round($g['n'] / $maks * 100) }}%"></i><span>{{ \Carbon\Carbon::parse($g['t'])->format('d') }}</span></div>@endforeach</div>
  </div>
  <div class="kartu" style="margin-top:0"><h2>{!! ikon('pengguna') !!} Ringkasan</h2>
    <dl class="dl"><dt>Penduduk aktif</dt><dd>{{ number_format($penduduk, 0, ',', '.') }} jiwa</dd><dt>Total selesai</dt><dd>{{ $per['selesai'] ?? 0 }} surat</dd><dt>Ditolak</dt><dd>{{ $per['ditolak'] ?? 0 }}</dd></dl>
    <a class="btn garis kecil" style="margin-top:14px" href="{{ route('admin.laporan') }}">{!! ikon('grafik') !!} Buka laporan bulanan</a>
  </div>
</div>
@endsection
