@extends('publik.layout', ['judul' => 'Verifikasi surat'])
@section('hero')<div class="judul"><span class="kicker">Verifikasi keaslian</span><h1>@if ($p)Surat ini <span class="serif">asli</span>@else Surat <span class="serif">tidak dikenali</span>@endif</h1><p>@if ($p)Diterbitkan resmi oleh Kelurahan {{ kel('nama') }}.@else Kode verifikasi tidak terdaftar. Surat mungkin palsu atau sudah dibatalkan.@endif</p></div>@endsection
@section('isi')
<div class="wadah konten" style="max-width:720px">
  @if ($p)
    <div class="panel putih">
      <table style="width:100%;border-collapse:collapse;font-size:15px">
        @foreach (['Nomor surat' => $p->nomor_surat, 'Jenis surat' => $p->layanan->nama, 'Tanggal terbit' => tgl($p->tanggal_surat), 'Atas nama' => $p->penduduk->nama, 'NIK' => \Illuminate\Support\Str::mask($p->penduduk->nik, '*', 6, 6), 'Keperluan' => $p->keperluan] as $kk => $v)
          <tr><td style="padding:10px 0;border-top:1px solid var(--garis);color:var(--abu);width:40%">{{ $kk }}</td><td style="padding:10px 0;border-top:1px solid var(--garis);font-weight:700">{{ $v }}</td></tr>
        @endforeach
      </table>
      <p class="muted kecil" style="margin:16px 0 0">Cocokkan data di atas dengan surat fisik. Jika berbeda, surat tersebut tidak sah.</p>
    </div>
  @else
    <div class="panel kosong"><p>Hubungi kelurahan untuk memastikan keaslian surat.</p></div>
  @endif
</div>
@endsection
