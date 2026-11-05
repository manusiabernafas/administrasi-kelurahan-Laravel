@extends('panel', ['judul' => 'Pengaduan ' . $a->kode, 'sub' => $a->kategori . ' - ' . tgl($a->created_at, true)])
@section('aksi')<a class="btn garis" href="{{ route('admin.pengaduan.index') }}">{!! ikon('kiri') !!} Kembali</a>@endsection
@section('isi')
<div class="grid g2" style="align-items:start">
  <div class="kartu" style="margin-top:0"><h2>Isi pengaduan</h2><p style="white-space:pre-line;margin-top:0">{{ $a->isi }}</p>
    <dl class="dl"><dt>Pelapor</dt><dd>{{ $a->nama }}</dd><dt>Kontak</dt><dd>{{ $a->kontak }}</dd><dt>Lokasi</dt><dd>{{ $a->lokasi ?: '-' }}</dd><dt>Lampiran</dt><dd>@if ($a->lampiran)<a href="{{ route('admin.pengaduan.show', [$a, 'lampiran' => 1]) }}" target="_blank">Lihat foto</a>@else - @endif</dd></dl></div>
  <form class="form kartu" method="post" action="{{ route('admin.pengaduan.tanggapi', $a) }}" style="margin-top:0" novalidate>@csrf<h2>Tanggapan</h2>
    <div class="f"><label for="tanggapan">Tanggapan untuk pelapor</label><textarea id="tanggapan" name="tanggapan" style="min-height:160px">{{ old('tanggapan', $a->tanggapan) }}</textarea><div class="hint">Pelapor bisa membaca tanggapan ini di menu Cek status dengan kode {{ $a->kode }}.</div>@error('tanggapan')<div class="galat">{{ $message }}</div>@enderror</div>
    <div class="f"><label>Status</label><div style="display:flex;gap:8px;flex-wrap:wrap"><label class="cek" style="border:1.5px solid var(--garis);border-radius:12px;padding:10px 14px"><input type="radio" name="status" value="ditanggapi" @checked(old('status', $a->status) !== 'selesai')> Ditanggapi, masih ditindaklanjuti</label><label class="cek" style="border:1.5px solid var(--garis);border-radius:12px;padding:10px 14px"><input type="radio" name="status" value="selesai" @checked(old('status', $a->status) === 'selesai')> Selesai</label></div></div>
    <button class="btn">{!! ikon('cek') !!} Simpan tanggapan</button></form>
</div>
@endsection
