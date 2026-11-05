@extends('panel', ['judul' => 'Data penduduk', 'sub' => 'Warga hanya bisa membuat akun jika NIK-nya ada di sini'])
@section('aksi')<a class="btn lime" href="{{ route('admin.penduduk.create') }}">{!! ikon('plus') !!} Tambah penduduk</a>@endsection
@section('isi')
<div class="grid g4" style="margin-bottom:18px">
  <div class="stat sorot"><div class="ikon">{!! ikon('pengguna') !!}</div><b>{{ number_format($ringkas['total'], 0, ',', '.') }}</b><span>penduduk aktif</span></div>
  <div class="stat"><div class="ikon">{!! ikon('user') !!}</div><b>{{ number_format($ringkas['l'], 0, ',', '.') }}</b><span>laki-laki</span></div>
  <div class="stat"><div class="ikon">{!! ikon('user') !!}</div><b>{{ number_format($ringkas['p'], 0, ',', '.') }}</b><span>perempuan</span></div>
  <div class="stat"><div class="ikon">{!! ikon('rumah') !!}</div><b>{{ number_format($ringkas['kk'], 0, ',', '.') }}</b><span>kepala keluarga (KK)</span></div>
</div>
@if (session('galat_impor'))<div class="alert err">Sebagian baris dilewati:<br>@foreach (session('galat_impor') as $g){{ $g }}<br>@endforeach</div>@endif
<div class="kartu">
  <form class="cari" method="get" style="margin-bottom:12px">
    <input type="search" name="q" value="{{ request('q') }}" placeholder="Cari nama, NIK, atau No. KK" aria-label="Cari">
    <select name="rw" aria-label="RW"><option value="">Semua RW</option>@foreach ($rw as $x)<option value="{{ $x }}" @selected(request('rw') === $x)>RW {{ $x }}</option>@endforeach</select>
    <select name="status" aria-label="Status"><option value="">Semua status</option>@foreach (['aktif', 'pindah', 'meninggal'] as $s)<option value="{{ $s }}" @selected(request('status') === $s)>{{ ucfirst($s) }}</option>@endforeach</select>
    <button class="btn garis">{!! ikon('cari') !!} Cari</button>
  </form>
  @if ($daftar->isEmpty())<div class="kosong">{!! ikon('pengguna') !!}<p>Data tidak ditemukan.</p></div>@else
  <div class="tabel-wrap"><table class="tabel"><thead><tr><th>Nama</th><th class="hp-sembunyi">NIK</th><th class="hp-sembunyi">L/P</th><th class="hp-sembunyi">Umur</th><th>RT/RW</th><th class="hp-sembunyi">Status</th><th></th></tr></thead><tbody>
    @foreach ($daftar as $p)<tr><td><a href="{{ route('admin.penduduk.show', $p) }}"><b>{{ $p->nama }}</b></a><div class="kecil muted">{{ $p->pekerjaan }}</div></td><td class="hp-sembunyi kode">{{ $p->nik }}</td><td class="hp-sembunyi">{{ $p->jenis_kelamin }}</td><td class="hp-sembunyi">{{ $p->umur }}</td><td>{{ $p->rt }}/{{ $p->rw }}</td><td class="hp-sembunyi">{!! lencana(ucfirst($p->status), $p->status === 'aktif' ? 'hijau' : 'abu') !!}</td><td class="aksi"><a class="btn garis kecil" href="{{ route('admin.penduduk.edit', $p) }}">{!! ikon('edit') !!}<span class="hp-sembunyi">Ubah</span></a></td></tr>@endforeach
  </tbody></table></div>
  @include('publik._pagin', ['p' => $daftar])
  @endif
</div>
<form class="form kartu" method="post" action="{{ route('admin.penduduk.impor') }}" enctype="multipart/form-data">
  @csrf
  <h2>{!! ikon('unggah') !!} Impor dari Excel (CSV)</h2>
  <div class="bantuan" style="margin-bottom:14px">Unduh <a href="{{ route('admin.penduduk.template') }}">template CSV</a>, isi di Excel, lalu simpan sebagai <code>CSV (pemisah titik koma)</code>. NIK yang sudah ada akan diperbarui. Tanggal lahir boleh <code>1990-01-31</code> atau <code>31/01/1990</code>.</div>
  <div class="dua"><div class="f"><input type="file" name="berkas" accept=".csv,text/csv" required>@error('berkas')<div class="galat">{{ $message }}</div>@enderror</div><div><button class="btn">{!! ikon('unggah') !!} Impor</button></div></div>
</form>
@endsection
