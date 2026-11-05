@extends('publik.layout', ['judul' => 'Cek status'])
@section('hero')<div class="judul"><span class="kicker">Lacak permohonan</span><h1>Cek status <span class="serif">surat & pengaduan</span></h1><p>Masukkan kode lacak yang kamu dapat saat mengajukan permohonan atau pengaduan.</p>
  <form class="form-cek" action="{{ route('cek-status') }}" method="get" style="margin-top:26px;max-width:560px"><label class="sr" for="kode">Kode</label><input id="kode" name="kode" value="{{ $kode }}" placeholder="Contoh KLR-2610-ABCDE" required maxlength="20"><button class="btn gelap">Cek <span class="bulat">{!! ikon('cari') !!}</span></button></form></div>@endsection
@section('isi')
<div class="wadah konten">
  @if ($kode !== '' && ! $p && ! $a)
    <div class="panel kosong"><h2 style="color:var(--tinta)">Kode tidak ditemukan</h2><p>Periksa kembali penulisan kode. Kode lacak permohonan diawali KLR-, kode pengaduan diawali ADU-.</p></div>
  @elseif ($p)
    <div class="dua-kolom">
      <div class="panel putih">
        <span class="muted kecil">Kode lacak</span><div class="kode-besar">{{ $p->kode }}</div>
        <h2 style="font-size:26px;margin:14px 0 6px">{{ $p->layanan->nama }}</h2>
        <p class="muted" style="margin:0 0 16px">Pemohon: {{ \Illuminate\Support\Str::mask($p->penduduk->nama, '*', 3, max(0, mb_strlen($p->penduduk->nama) - 5)) }} &middot; diajukan {{ tgl($p->created_at, true) }}</p>
        {!! lencana($p->label_status, $p->warna_status) !!}
        @if ($p->status === 'ditolak' && $p->catatan)<div class="alert err" style="margin-top:16px">Catatan petugas: {{ $p->catatan }}</div>@endif
        @if ($p->status === 'selesai')<div class="alert ok" style="margin-top:16px">Surat sudah terbit (nomor {{ $p->nomor_surat }}). Unduh dari akun warga atau ambil di loket dengan menunjukkan kode lacak.</div>@endif
      </div>
      <div class="panel"><h2>Riwayat proses</h2><ul class="garis-waktu">@foreach ($p->riwayat as $r)<li><b>{{ \App\Models\Permohonan::STATUS[$r->status][0] ?? $r->status }}</b><small>{{ tgl($r->created_at, true) }}</small>@if ($r->catatan)<div class="kecil">{{ $r->catatan }}</div>@endif</li>@endforeach</ul></div>
    </div>
  @elseif ($a)
    <div class="dua-kolom">
      <div class="panel putih"><span class="muted kecil">Kode pengaduan</span><div class="kode-besar">{{ $a->kode }}</div><h2 style="font-size:24px;margin:14px 0 6px">{{ $a->kategori }}</h2><p class="muted">Dikirim {{ tgl($a->created_at, true) }}</p>{!! lencana(\App\Models\Pengaduan::STATUS[$a->status][0], \App\Models\Pengaduan::STATUS[$a->status][1]) !!}<p style="white-space:pre-line;margin-top:16px">{{ $a->isi }}</p></div>
      <div class="panel"><h2>Tanggapan kelurahan</h2>@if ($a->tanggapan)<p style="white-space:pre-line;margin:0">{{ $a->tanggapan }}</p><p class="muted kecil">{{ tgl($a->ditanggapi_at, true) }}</p>@else<p class="muted" style="margin:0">Pengaduan sedang dipelajari petugas. Cek kembali nanti.</p>@endif</div>
    </div>
  @else
    <div class="panel kosong">{!! ikon('qr') !!}<p>Kode lacak ada di halaman akun warga dan di bukti pengajuan.</p></div>
  @endif
</div>
@endsection
