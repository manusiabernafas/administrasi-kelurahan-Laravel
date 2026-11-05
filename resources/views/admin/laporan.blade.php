@extends('panel', ['judul' => 'Laporan bulanan', 'sub' => 'Rekap permohonan & buku register surat keluar'])
@section('aksi')
<form method="get" class="cari"><input type="month" name="bulan" value="{{ $bulan }}" aria-label="Bulan"><button class="btn garis">Tampilkan</button></form>
<a class="btn lime" href="{{ route('admin.laporan.csv', ['bulan' => $bulan]) }}">{!! ikon('unduh') !!} Unduh CSV</a>
<button class="btn garis" type="button" onclick="print()">{!! ikon('cetak') !!} Cetak</button>
@endsection
@section('isi')
<div class="kartu"><h2>Rekap per jenis layanan ({{ \Carbon\Carbon::createFromFormat('Y-m', $bulan)->translatedFormat('F Y') }})</h2>
  <div class="tabel-wrap"><table class="tabel"><thead><tr><th>Layanan</th><th>Masuk</th><th>Selesai</th><th>Ditolak</th><th class="hp-sembunyi">Online</th></tr></thead><tbody>
    @foreach ($rekap as $r)<tr><td><b>{{ $r['nama'] }}</b></td><td>{{ $r['masuk'] }}</td><td>{{ $r['selesai'] }}</td><td>{{ $r['ditolak'] }}</td><td class="hp-sembunyi">{{ $r['masuk'] ? round($r['online'] / $r['masuk'] * 100) . '%' : '-' }}</td></tr>@endforeach
    <tr style="font-weight:800"><td>Jumlah</td><td>{{ $rekap->sum('masuk') }}</td><td>{{ $rekap->sum('selesai') }}</td><td>{{ $rekap->sum('ditolak') }}</td><td class="hp-sembunyi"></td></tr>
  </tbody></table></div></div>
<div class="kartu"><h2>Register surat keluar</h2>
  @if ($surat->isEmpty())<div class="kosong">{!! ikon('dokumen') !!}<p>Belum ada surat terbit di bulan ini.</p></div>@else
  <div class="tabel-wrap"><table class="tabel"><thead><tr><th>Nomor surat</th><th class="hp-sembunyi">Tanggal</th><th>Nama</th><th class="hp-sembunyi">Jenis</th><th class="hp-sembunyi">Keperluan</th></tr></thead><tbody>
    @foreach ($surat as $p)<tr><td class="kode"><a href="{{ route('admin.permohonan.show', $p) }}">{{ $p->nomor_surat }}</a></td><td class="hp-sembunyi kecil">{{ tgl($p->tanggal_surat) }}</td><td>{{ $p->penduduk->nama }}</td><td class="hp-sembunyi">{{ $p->layanan->nama }}</td><td class="hp-sembunyi kecil">{{ \Illuminate\Support\Str::limit($p->keperluan, 40) }}</td></tr>@endforeach
  </tbody></table></div>@endif</div>
@endsection
