@extends('panel', ['judul' => $p->layanan->nama, 'sub' => 'Kode lacak ' . $p->kode])
@section('aksi')<a class="btn garis" href="{{ route('warga.beranda') }}">{!! ikon('kiri') !!} Kembali</a>@endsection
@section('isi')
<div class="grid g3" style="align-items:start">
  <div class="lebar-2">
    <div class="kartu">
      @include('_status')
      @if ($p->status === 'selesai')
        <div class="alert ok">Surat sudah terbit dengan nomor <b>{{ $p->nomor_surat }}</b>. Unduh PDF di bawah atau ambil versi bertanda tangan basah di loket.</div>
        <a class="btn lime" href="{{ route('berkas.surat', $p->id) }}">{!! ikon('unduh') !!} Unduh surat (PDF)</a>
      @elseif ($p->status === 'ditolak')
        <div class="alert err">Permohonan ditolak. Catatan petugas: {{ $p->catatan ?: '-' }}</div>
        <a class="btn" href="{{ route('warga.ajukan', strtolower($p->layanan->kode)) }}">{!! ikon('plus') !!} Ajukan ulang</a>
      @else
        <p class="muted" style="margin:0">Permohonan sedang {{ strtolower($p->label_status) }}. Kamu tidak perlu datang ke kantor sampai status menjadi <b>Selesai</b>.</p>
        @if ($p->status === 'diajukan')<form method="post" action="{{ route('warga.batal', $p->id) }}" data-yakin="Batalkan permohonan ini?" style="margin-top:14px">@csrf<button class="btn merah kecil">{!! ikon('x') !!} Batalkan permohonan</button></form>@endif
      @endif
    </div>
    <div class="kartu"><h2>Data permohonan</h2>
      <dl class="dl"><dt>Keperluan</dt><dd>{{ $p->keperluan }}</dd>
        @foreach ($p->layanan->isian as $f)<dt>{{ $f['label'] }}</dt><dd>{{ ($p->data[$f['nama']] ?? '') !== '' ? (preg_match('/^\d{4}-\d{2}-\d{2}$/', $p->data[$f['nama']]) ? tgl($p->data[$f['nama']]) : $p->data[$f['nama']]) : '-' }}</dd>@endforeach
        <dt>Diajukan</dt><dd>{{ tgl($p->created_at, true) }}</dd>
        <dt>Berkas</dt><dd>@forelse ($p->lampiran ?? [] as $i => $f)<a href="{{ route('berkas.lampiran', [$p->id, $i]) }}" target="_blank">{{ $f['nama'] }}</a><br>@empty - @endforelse</dd></dl>
    </div>
  </div>
  <div class="kartu" style="margin-top:0"><h2>Riwayat</h2><ul class="garis-waktu">@foreach ($p->riwayat as $r)<li><b>{{ \App\Models\Permohonan::STATUS[$r->status][0] ?? $r->status }}</b><small>{{ tgl($r->created_at, true) }}</small>@if ($r->catatan)<div class="kecil">{{ $r->catatan }}</div>@endif</li>@endforeach</ul></div>
</div>
@endsection
