<!doctype html>
<html lang="id"><head><meta charset="utf-8"><title>{{ $p->layanan->nama }}</title>
<style>
  @page { margin: 34px 56px 40px; }
  body { font-family: "Times New Roman", Times, serif; font-size: 12pt; color: #000; line-height: 1.5; }
  .kop { width: 100%; border-bottom: 3px double #000; padding-bottom: 8px; margin-bottom: 18px; }
  .kop td { vertical-align: middle; }
  .kop .t1 { font-size: 14pt; font-weight: bold; text-transform: uppercase; margin: 0; }
  .kop .t2 { font-size: 16pt; font-weight: bold; text-transform: uppercase; margin: 0; }
  .kop .t3 { font-size: 10pt; margin: 2px 0 0; }
  h1 { text-align: center; font-size: 13pt; text-decoration: underline; text-transform: uppercase; margin: 0; }
  .nomor { text-align: center; margin: 2px 0 20px; }
  table.data { margin: 10px 0 12px 30px; border-collapse: collapse; }
  table.data td { padding: 2px 6px; vertical-align: top; }
  p { margin: 0 0 10px; text-align: justify; text-indent: 40px; }
  .ttd { width: 100%; margin-top: 30px; }
  .ttd td { vertical-align: top; }
  .kecil { font-size: 8.5pt; color: #333; }
  .draf { position: fixed; top: 40%; left: 10%; font-size: 70pt; color: #e5e5e5; transform: rotate(-30deg); z-index: -1; }
</style></head>
<body>
@if (! $p->nomor_surat)<div class="draf">PRATINJAU</div>@endif
<table class="kop"><tr>
  <td style="width:80px"><img src="{{ $logo }}" style="width:70px;height:80px" alt=""></td>
  <td style="text-align:center">
    <div class="t1">Pemerintah {{ $k['kota'] }}</div>
    <div class="t1">Kecamatan {{ $k['kecamatan'] }}</div>
    <div class="t2">Kelurahan {{ $k['nama'] }}</div>
    <div class="t3">{{ $k['alamat'] }}, {{ $k['kota'] }} {{ $k['kode_pos'] }}{{ $k['telepon'] ? ' - Telp. ' . $k['telepon'] : '' }}{{ $k['email'] ? ' - ' . $k['email'] : '' }}</div>
  </td>
  <td style="width:80px"></td>
</tr></table>
<h1>{{ $p->layanan->nama }}</h1>
<div class="nomor">Nomor: {{ $p->nomor_surat ?: '......./' . $p->layanan->kode . '/' . $k['kode_surat'] . '/....../' . date('Y') }}</div>
<p style="text-indent:0">Yang bertanda tangan di bawah ini, Lurah {{ $k['nama'] }} Kecamatan {{ $k['kecamatan'] }} {{ $k['kota'] }}, dengan ini menerangkan bahwa:</p>
<table class="data">
  @foreach (['Nama' => $p->penduduk->nama, 'NIK' => $p->penduduk->nik, 'Tempat, tanggal lahir' => $p->penduduk->ttl, 'Jenis kelamin' => $p->penduduk->jenis_kelamin === 'L' ? 'Laki-laki' : 'Perempuan', 'Agama' => $p->penduduk->agama, 'Status perkawinan' => $p->penduduk->status_perkawinan, 'Pekerjaan' => $p->penduduk->pekerjaan ?: '-', 'Kewarganegaraan' => $p->penduduk->kewarganegaraan, 'Alamat' => $p->penduduk->alamat_lengkap . ', Kel. ' . $k['nama']] as $l => $v)
    <tr><td style="width:170px">{{ $l }}</td><td>:</td><td>{{ $v }}</td></tr>
  @endforeach
  @foreach ($p->layanan->isian as $f)<tr><td>{{ $f['label'] }}</td><td>:</td><td>{{ ($p->data[$f['nama']] ?? '') !== '' ? (preg_match('/^\d{4}-\d{2}-\d{2}$/', $p->data[$f['nama']]) ? tgl($p->data[$f['nama']]) : $p->data[$f['nama']]) : '-' }}</td></tr>@endforeach
</table>
@foreach (preg_split('/\R+/', trim($isi)) as $par)<p>{!! $par !!}</p>@endforeach
<table class="ttd"><tr>
  <td style="width:55%">@if ($qr)<img src="{{ $qr }}" style="width:96px;height:96px" alt=""><div class="kecil">Pindai untuk memeriksa keaslian surat.<br>Kode lacak: {{ $p->kode }}</div>@endif</td>
  <td style="text-align:center">{{ $k['kota'] }}, {{ tgl($p->tanggal_surat ?? now()) }}<br>Lurah {{ $k['nama'] }}<br><br><br><br><b><u>{{ $k['nama_lurah'] }}</u></b>@if ($k['nip_lurah'])<br>NIP. {{ $k['nip_lurah'] }}@endif</td>
</tr></table>
</body></html>
