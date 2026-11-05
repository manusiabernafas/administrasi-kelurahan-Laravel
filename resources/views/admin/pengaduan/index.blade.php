@extends('panel', ['judul' => 'Pengaduan warga'])
@section('isi')
<div class="filter"><a class="chip {{ ! request('status') ? 'on' : '' }}" href="{{ route('admin.pengaduan.index') }}">Semua<span>{{ $hitung->sum() }}</span></a>@foreach (\App\Models\Pengaduan::STATUS as $s => [$l])<a class="chip {{ request('status') === $s ? 'on' : '' }}" href="{{ route('admin.pengaduan.index', ['status' => $s]) }}">{{ $l }}<span>{{ $hitung[$s] ?? 0 }}</span></a>@endforeach</div>
<div class="kartu">@if ($daftar->isEmpty())<div class="kosong">{!! ikon('pesan') !!}<p>Tidak ada pengaduan.</p></div>@else
<div class="tabel-wrap"><table class="tabel"><thead><tr><th>Pengaduan</th><th class="hp-sembunyi">Pelapor</th><th class="hp-sembunyi">Masuk</th><th>Status</th><th></th></tr></thead><tbody>
  @foreach ($daftar as $a)<tr><td><b>{{ $a->kategori }}</b><div class="kecil muted">{{ \Illuminate\Support\Str::limit($a->isi, 70) }}</div></td><td class="hp-sembunyi">{{ $a->nama }}</td><td class="hp-sembunyi kecil">{{ tgl($a->created_at, true) }}</td><td>{!! lencana(\App\Models\Pengaduan::STATUS[$a->status][0], \App\Models\Pengaduan::STATUS[$a->status][1]) !!}</td><td class="aksi"><a class="btn {{ $a->status === 'baru' ? '' : 'garis' }} kecil" href="{{ route('admin.pengaduan.show', $a) }}">{{ $a->status === 'baru' ? 'Tanggapi' : 'Lihat' }}</a></td></tr>@endforeach
</tbody></table></div>@include('publik._pagin', ['p' => $daftar])@endif</div>
@endsection
