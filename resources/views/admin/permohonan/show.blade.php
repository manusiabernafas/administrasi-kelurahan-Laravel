@extends('panel', ['judul' => $p->layanan->nama, 'sub' => 'Kode ' . $p->kode . ' - ' . ($p->saluran === 'loket' ? 'diajukan di loket' : 'diajukan online')])
@section('aksi')<a class="btn garis" href="{{ route('admin.permohonan.index') }}">{!! ikon('kiri') !!} Daftar permohonan</a>@endsection
@section('isi')
<div class="grid g3" style="align-items:start">
  <div class="lebar-2">
    <div class="kartu" style="margin-top:0">
      @include('_status')
      @if ($p->status === 'selesai')
        <div class="alert ok">Surat terbit: <b>{{ $p->nomor_surat }}</b> tanggal {{ tgl($p->tanggal_surat) }}.</div>
        <a class="btn lime" href="{{ route('berkas.surat', $p->id) }}">{!! ikon('unduh') !!} Unduh PDF</a>
        <a class="btn garis" href="{{ route('admin.permohonan.pratinjau', $p) }}" target="_blank">{!! ikon('cetak') !!} Buka & cetak</a>
      @elseif ($p->status === 'ditolak')
        <div class="alert err">Ditolak. Catatan: {{ $p->catatan ?: '-' }}</div>
      @else
        <form class="form" method="post" action="{{ route('admin.permohonan.status', $p) }}">
          @csrf
          <h2>{!! ikon('kilat') !!} Tindak lanjut</h2>
          <div class="bantuan" style="margin-bottom:14px">Periksa data dan berkas di bawah. Jika lengkap, ubah status ke tahap berikutnya. Saat memilih <b>Selesai</b>, nomor surat dibuat otomatis dan warga bisa langsung mengunduh suratnya.</div>
          <div class="f"><label>Ubah status menjadi</label>
            <div style="display:flex;gap:8px;flex-wrap:wrap">@foreach ($alur as $s)<label class="cek" style="border:1.5px solid var(--garis);border-radius:12px;padding:10px 14px"><input type="radio" name="status" value="{{ $s }}" @checked(old('status', $alur[0]) === $s)> {{ \App\Models\Permohonan::STATUS[$s][0] }}{{ $s === 'selesai' ? ' & terbitkan surat' : '' }}</label>@endforeach</div>
            @error('status')<div class="galat">{{ $message }}</div>@enderror</div>
          <div class="f"><label for="catatan">Catatan untuk warga</label><textarea id="catatan" name="catatan" style="min-height:80px" placeholder="Wajib diisi jika menolak, misal: foto KK tidak terbaca, mohon unggah ulang.">{{ old('catatan') }}</textarea>@error('catatan')<div class="galat">{{ $message }}</div>@enderror</div>
          <button class="btn">{!! ikon('cek') !!} Simpan status</button>
          <a class="btn garis" href="{{ route('admin.permohonan.pratinjau', $p) }}" target="_blank">{!! ikon('mata') !!} Pratinjau surat</a>
        </form>
      @endif
    </div>
    <div class="kartu"><h2>{!! ikon('dokumen') !!} Data permohonan</h2>
      <dl class="dl"><dt>Keperluan</dt><dd>{{ $p->keperluan }}</dd>
        @foreach ($p->layanan->isian as $f)<dt>{{ $f['label'] }}</dt><dd>{{ ($p->data[$f['nama']] ?? '') !== '' ? (preg_match('/^\d{4}-\d{2}-\d{2}$/', $p->data[$f['nama']]) ? tgl($p->data[$f['nama']]) : $p->data[$f['nama']]) : '-' }}</dd>@endforeach
        <dt>Berkas</dt><dd>@forelse ($p->lampiran ?? [] as $i => $f)<a href="{{ route('berkas.lampiran', [$p->id, $i]) }}" target="_blank">{!! ikon('dokumen') !!} {{ $f['nama'] }}</a><br>@empty <span class="muted">Tidak ada berkas{{ $p->saluran === 'loket' ? ' (diperiksa langsung di loket)' : '' }}</span>@endforelse</dd></dl>
    </div>
  </div>
  <div>
    <div class="kartu" style="margin-top:0"><h2>{!! ikon('user') !!} Pemohon</h2>
      <dl class="dl"><dt>Nama</dt><dd><a href="{{ route('admin.penduduk.show', $p->penduduk) }}">{{ $p->penduduk->nama }}</a></dd><dt>NIK</dt><dd>{{ $p->penduduk->nik }}</dd><dt>TTL</dt><dd>{{ $p->penduduk->ttl }}</dd><dt>Alamat</dt><dd>{{ $p->penduduk->alamat_lengkap }}</dd>@if ($p->pemohon?->telepon)<dt>HP</dt><dd>{{ $p->pemohon->telepon }}</dd>@endif</dl>
    </div>
    <div class="kartu"><h2>{!! ikon('jam') !!} Riwayat</h2><ul class="garis-waktu">@foreach ($p->riwayat as $r)<li><b>{{ \App\Models\Permohonan::STATUS[$r->status][0] ?? $r->status }}</b><small>{{ tgl($r->created_at, true) }}{{ $r->user ? ' oleh ' . $r->user->name : '' }}</small>@if ($r->catatan)<div class="kecil">{{ $r->catatan }}</div>@endif</li>@endforeach</ul></div>
  </div>
</div>
@endsection
