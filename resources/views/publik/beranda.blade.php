@extends('publik.layout', ['heroBesar' => true])
@php $k = \App\Support\Kelurahan::semua(); @endphp
@section('hero')
  <img class="ilustrasi" src="{{ asset('img/kantor.svg') }}" alt="">
  <div class="jaminan">{!! ikon('perisai') !!}Tanpa biaya, tanpa pungli<br>Surat ber-QR, bisa dicek keasliannya<br>Lacak proses dari HP</div>
  <div class="isi">
    <div class="nilai"><span class="lingkar"><span style="background:#c6f36b;color:#163c2a">R</span><span style="background:#fff;color:#163c2a">W</span><span style="background:#3b8a5c;color:#fff">T</span></span><span><b>&#9733; 4,9</b> rata-rata penilaian warga<small>{{ $angka['puas'] }}% warga puas dengan pelayanan</small></span></div>
    <div class="chip-baris"><span class="chip">Surat keterangan</span><span class="chip">Pengantar</span><span class="chip">Pengaduan warga</span></div>
    <h1>Urus surat kelurahan, <span class="serif">tanpa antre</span> lagi</h1>
    <p class="lead">{{ $k['tagline'] }}. Ajukan surat domisili, SKTM, pengantar SKCK, dan lainnya dari HP, pantau prosesnya, lalu ambil suratnya saat sudah jadi.</p>
    <div class="aksi">
      <a class="btn" href="{{ auth()->check() ? route('warga.pilih') : route('daftar') }}">Ajukan permohonan <span class="bulat">{!! ikon('panah') !!}</span></a>
      <a class="tautan" href="{{ route('cek-status') }}">Cek status permohonan {!! ikon('panah', 'ic') !!}</a>
    </div>
  </div>
  <div class="kartu-angka"><div><b>{{ number_format(max($angka['selesai'], 0), 0, ',', '.') }}</b><small>surat diterbitkan</small></div><div style="width:1px;align-self:stretch;background:rgba(255,255,255,.15)"></div><div><b>{{ str_replace('.', ',', $angka['rata']) }} hari</b><small>rata-rata selesai</small></div></div>
@endsection
@section('isi')
<div class="wadah lembaga" aria-label="Lembaga kemasyarakatan">
  <span>{!! ikon('pengguna') !!} RT / RW</span><span>{!! ikon('rumah') !!} PKK</span><span>{!! ikon('bayi') !!} Posyandu</span><span>{!! ikon('bintang') !!} Karang Taruna</span><span>{!! ikon('jalur') !!} LPM</span><span>{!! ikon('perisai') !!} Linmas</span>
</div>

<section class="seksi" style="padding-top:40px">
  <div class="wadah tentang">
    <div><span class="kicker">Tentang kelurahan</span><div class="foto"><img src="{{ asset('img/kantor.svg') }}" alt="Ilustrasi kantor kelurahan"></div></div>
    <div>
      <p class="teks">{{ $k['sambutan'] }}</p>
      <div class="aksi" style="display:flex;gap:22px;align-items:center;flex-wrap:wrap">
        <a class="btn" href="{{ route('layanan') }}">Lihat semua layanan <span class="bulat">{!! ikon('panah') !!}</span></a>
        @if (\App\Support\Kelurahan::waLink())<a class="tautan" href="{{ \App\Support\Kelurahan::waLink('Halo, saya ingin bertanya tentang layanan kelurahan.') }}" target="_blank" rel="noopener">Tanya lewat WhatsApp {!! ikon('panah') !!}</a>@endif
      </div>
      <div class="ttd">{!! ikon('user') !!} {{ $k['nama_lurah'] }}, Lurah {{ $k['nama'] }}</div>
    </div>
  </div>
</section>

<section class="seksi" style="padding-top:20px">
  <div class="wadah">
    <div class="kepala-seksi">
      <div><span class="kicker">Layanan kami</span><h2>Surat yang paling <span class="serif">sering</span><br><span class="serif">dibutuhkan</span> warga</h2></div>
      <div style="display:flex;flex-direction:column;align-items:flex-end;gap:18px"><p>Semua layanan gratis. Pilih jenis surat untuk melihat syarat dan perkiraan waktu selesai.</p><div class="geser-nav"><button type="button" aria-label="Sebelumnya" data-geser="-1">{!! ikon('kiri') !!}</button><button type="button" aria-label="Berikutnya" data-geser="1">{!! ikon('kanan') !!}</button></div></div>
    </div>
    <div class="rel" id="rel-layanan">@foreach ($layanan as $l)@include('publik._kartu-layanan')@endforeach</div>
  </div>
</section>

<section class="seksi krem">
  <div class="wadah">
    <div class="kepala-seksi">
      <div><span class="kicker">Alur pelayanan</span><h2>Dari pengajuan<br><span class="serif">sampai surat jadi</span></h2></div>
      <p>Empat langkah sederhana. Setiap perubahan status bisa kamu pantau sendiri lewat kode lacak.</p>
    </div>
    <div class="alur">
      <div class="langkah"><span class="no">Langkah 01</span><h3>Daftar dengan NIK</h3><p>Buat akun memakai NIK dan tanggal lahir yang terdaftar di kelurahan. Cukup sekali.</p>
        <div class="vis"><div class="titik-alur"><span>{!! ikon('user') !!}</span><i></i><span class="utama">{!! ikon('kunci') !!}</span><i></i><span>{!! ikon('cek') !!}</span></div></div></div>
      <div class="langkah"><span class="no">Langkah 02</span><h3>Isi formulir & unggah berkas</h3><p>Pilih jenis surat, tulis keperluan, lalu unggah foto KTP/KK atau surat pengantar RT.</p>
        <div class="vis"><div class="titik-alur"><span>{!! ikon('dokumen') !!}</span><i></i><span class="utama">{!! ikon('unggah') !!}</span><i></i><span>{!! ikon('kilat') !!}</span></div></div></div>
      <div class="langkah"><span class="no">Langkah 03</span><h3>Pantau prosesnya</h3><p>Petugas memeriksa berkas. Status berubah dari diajukan, diverifikasi, diproses, hingga selesai.</p>
        <div class="vis"><div class="titik-alur"><span>{!! ikon('mata') !!}</span><i></i><span class="utama">{!! ikon('jam') !!}</span><i></i><span>{!! ikon('pesan') !!}</span></div></div></div>
      <div class="langkah lebar">
        <div><span class="no">Langkah 04</span><h3>Surat terbit dengan kode QR</h3><p>Unduh surat dalam bentuk PDF atau ambil di loket. Setiap surat punya nomor resmi dan kode QR yang bisa dipindai siapa saja untuk memastikan keasliannya.</p>
          <a class="btn" style="margin-top:22px" href="{{ route('cek-status') }}">Cek status sekarang <span class="bulat">{!! ikon('panah') !!}</span></a></div>
        <div class="daftar-cek"><b>Surat siap</b><div><span>{!! ikon('cek') !!}</span>Berkas lengkap</div><div><span>{!! ikon('cek') !!}</span>Nomor surat terbit</div><div><span>{!! ikon('cek') !!}</span>Ditandatangani lurah</div><div><span>{!! ikon('cek') !!}</span>QR verifikasi aktif</div></div>
      </div>
      <div class="langkah" style="background:var(--lime)"><span class="no" style="color:var(--tinta)">Butuh cepat?</span><h3>Datang ke loket</h3><p style="color:var(--tinta)">Petugas bisa langsung membuatkan surat di loket. Bawa KTP dan KK asli, surat bisa jadi dalam hitungan menit.</p></div>
    </div>
  </div>
</section>

<section class="seksi">
  <div class="wadah">
    <div class="cek-kotak">
      <div><span class="kicker" style="color:#fff">Lacak permohonan</span><h2 style="margin-top:12px">Sudah sampai mana <span class="serif">suratmu?</span></h2><p style="opacity:.8;margin:12px 0 0">Masukkan kode lacak (contoh KLR-2610-ABCDE) atau kode pengaduan (ADU-XXXXXX).</p></div>
      <form class="form-cek" action="{{ route('cek-status') }}" method="get"><label class="sr" for="kode-cek">Kode lacak</label><input id="kode-cek" name="kode" placeholder="Ketik kode lacak" required maxlength="20"><button class="btn gelap">Cek <span class="bulat">{!! ikon('cari') !!}</span></button></form>
    </div>
  </div>
</section>

<section class="seksi" style="padding-top:20px">
  <div class="wadah angka">
    <div><b>{{ number_format($angka['selesai'], 0, ',', '.') }}</b><span>surat sudah diterbitkan</span></div>
    <div><b>{{ number_format($angka['bulan'], 0, ',', '.') }}</b><span>permohonan bulan ini</span></div>
    <div><b>{{ str_replace('.', ',', $angka['rata']) }}</b><span>hari rata-rata proses</span></div>
    <div><b>Rp0</b><span>biaya layanan</span></div>
  </div>
</section>

@if ($kabar->isNotEmpty())
<section class="seksi krem">
  <div class="wadah">
    <div class="kepala-seksi"><div><span class="kicker">Kabar kelurahan</span><h2>Pengumuman & <span class="serif">kegiatan</span></h2></div><a class="btn" href="{{ route('kabar') }}">Semua kabar <span class="bulat">{!! ikon('panah') !!}</span></a></div>
    <div class="kisi-kabar">@foreach ($kabar as $x)@include('publik._kartu-kabar')@endforeach</div>
  </div>
</section>
@endif

<section class="seksi" id="jam">
  <div class="wadah info">
    <div class="kotak-info" style="background:var(--krem)">
      <h3>{!! ikon('jam') !!} Jam layanan loket</h3>
      @foreach (preg_split('/\R/', $k['jam_layanan']) as $baris)@if (trim($baris))<div class="baris">{!! ikon('kalender') !!}<span>{{ $baris }}</span></div>@endif @endforeach
      <h3 style="margin-top:22px">{!! ikon('pin') !!} Alamat</h3>
      <div class="baris">{!! ikon('rumah') !!}<span>{{ $k['alamat'] }}, Kec. {{ $k['kecamatan'] }}, {{ $k['kota'] }}</span></div>
      @if ($k['telepon'])<div class="baris">{!! ikon('telepon') !!}<span>{{ $k['telepon'] }}</span></div>@endif
      @if ($k['email'])<div class="baris">{!! ikon('surel') !!}<span>{{ $k['email'] }}</span></div>@endif
    </div>
    <div class="peta">@if ($k['peta'])<iframe src="{{ $k['peta'] }}" loading="lazy" title="Peta lokasi kantor kelurahan" referrerpolicy="no-referrer-when-downgrade"></iframe>@else<div style="height:100%;min-height:280px;display:grid;place-items:center;text-align:center;padding:30px;color:var(--hijau)"><div>{!! ikon('pin', 'ic') !!}<p style="margin:10px 0 0;font-weight:700">{{ $k['alamat'] }}</p><p class="kecil muted" style="margin:4px 0 0">Tempel tautan embed Google Maps di Pengaturan untuk menampilkan peta.</p></div></div>@endif</div>
  </div>
</section>

<section class="seksi krem" id="faq">
  <div class="wadah dua-kolom">
    <div><span class="kicker">Tanya jawab</span><h2 style="font-size:clamp(32px,4vw,46px);margin-top:12px">Yang sering <span class="serif">ditanyakan</span></h2><p class="muted">Tidak menemukan jawabannya? Kirim pertanyaan lewat WhatsApp atau datang ke loket.</p></div>
    <div class="faq">
      <details open><summary>Apakah ada biaya untuk mengurus surat?</summary><p>Tidak. Semua layanan administrasi di kelurahan gratis. Jika ada yang meminta bayaran, laporkan melalui menu Pengaduan.</p></details>
      <details><summary>Saya belum bisa daftar, NIK tidak ditemukan.</summary><p>Data Anda mungkin belum tercatat di kelurahan. Datang ke loket membawa KTP dan KK agar petugas memperbarui data kependudukan.</p></details>
      <details><summary>Berapa lama surat selesai?</summary><p>Tergantung jenis surat, umumnya 1 sampai 3 hari kerja. Perkiraan waktu tertulis di setiap jenis layanan.</p></details>
      <details><summary>Bagaimana memastikan surat yang saya terima asli?</summary><p>Pindai kode QR di surat. Halaman verifikasi kelurahan akan menampilkan nomor, nama, dan jenis surat yang sama.</p></details>
      <details><summary>Permohonan saya ditolak, apa yang harus dilakukan?</summary><p>Baca catatan petugas di halaman detail permohonan, perbaiki berkas yang kurang, lalu ajukan kembali.</p></details>
    </div>
  </div>
</section>

<section class="seksi" style="padding-bottom:0">
  <div class="wadah cta"><h2>Siap mengurus surat <span class="serif">dari rumah?</span></h2><a class="btn gelap" href="{{ auth()->check() ? route('warga.pilih') : route('daftar') }}">Daftar dengan NIK <span class="bulat">{!! ikon('panah') !!}</span></a></div>
</section>
@endsection
@push('skrip')
<script>
document.querySelectorAll('[data-geser]').forEach(b => b.addEventListener('click', () => { const r = document.getElementById('rel-layanan'); r.scrollBy({ left: Number(b.dataset.geser) * (r.firstElementChild.offsetWidth + 18), behavior: 'smooth' }); }));
</script>
@endpush
