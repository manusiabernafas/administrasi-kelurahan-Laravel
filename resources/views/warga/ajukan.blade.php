@extends('panel', ['judul' => $l->nama, 'sub' => 'Langkah 2 dari 2: isi formulir lalu kirim'])
@section('aksi')<a class="btn garis" href="{{ route('warga.pilih') }}">{!! ikon('kiri') !!} Ganti jenis surat</a>@endsection
@section('isi')
<div class="grid g3" style="align-items:start">
  <form class="form kartu lebar-2" method="post" action="{{ route('warga.simpan', strtolower($l->kode)) }}" enctype="multipart/form-data" novalidate>
    @csrf
    <h2>Data pemohon</h2>
    @if ($penduduk)
      <dl class="dl" style="margin-bottom:18px"><dt>Nama</dt><dd>{{ $penduduk->nama }}</dd><dt>NIK</dt><dd>{{ $penduduk->nik }}</dd><dt>Tempat, tgl lahir</dt><dd>{{ $penduduk->ttl }}</dd><dt>Alamat</dt><dd>{{ $penduduk->alamat_lengkap }}</dd></dl>
      <p class="kecil muted" style="margin:-6px 0 18px">Data di atas diambil dari data kependudukan kelurahan. Jika ada yang salah, hubungi petugas sebelum mengajukan.</p>
    @endif
    <h2>Isian permohonan</h2>
    @foreach ($l->isian as $f)
      <div class="f"><label for="d-{{ $f['nama'] }}">{{ $f['label'] }}@if ($f['wajib']) <span style="color:var(--merah)">*</span>@endif</label>
        @if ($f['tipe'] === 'textarea')<textarea id="d-{{ $f['nama'] }}" name="data[{{ $f['nama'] }}]" class="@error('data.' . $f['nama']) is-galat @enderror">{{ old('data.' . $f['nama']) }}</textarea>
        @else<input id="d-{{ $f['nama'] }}" type="{{ $f['tipe'] }}" name="data[{{ $f['nama'] }}]" value="{{ old('data.' . $f['nama']) }}" class="@error('data.' . $f['nama']) is-galat @enderror">@endif
        @error('data.' . $f['nama'])<div class="galat">{{ $message }}</div>@enderror</div>
    @endforeach
    <div class="f"><label for="keperluan">Keperluan <span style="color:var(--merah)">*</span></label><input id="keperluan" name="keperluan" value="{{ old('keperluan') }}" maxlength="255" placeholder="Contoh: melamar pekerjaan, pengajuan beasiswa" class="@error('keperluan') is-galat @enderror">@error('keperluan')<div class="galat">{{ $message }}</div>@enderror</div>
    <div class="f"><label for="lampiran">Unggah berkas pendukung</label><input id="lampiran" type="file" name="lampiran[]" multiple accept="image/jpeg,image/png,application/pdf"><div class="hint">Foto KTP, KK, surat pengantar RT/RW, atau berkas lain sesuai syarat. JPG/PNG/PDF, maksimal 5 file @ 3 MB.</div>@error('lampiran')<div class="galat">{{ $message }}</div>@enderror @foreach ($errors->get('lampiran.*') as $m)<div class="galat">{{ $m[0] }}</div>@endforeach</div>
    <div class="f"><label class="cek"><input type="checkbox" name="setuju" value="1" @checked(old('setuju'))> Saya menyatakan data dan berkas yang saya kirim benar dan bisa dipertanggungjawabkan.</label>@error('setuju')<div class="galat">{{ $message }}</div>@enderror</div>
    <button class="btn">{!! ikon('panah') !!} Kirim permohonan</button>
  </form>
  <div class="kartu" style="margin-top:0"><h2>{!! ikon('info') !!} Persyaratan</h2><ol style="margin:0;padding-left:20px">@foreach ($l->syarat as $s)<li style="margin-bottom:6px">{{ $s }}</li>@endforeach</ol><p class="kecil muted" style="margin:14px 0 0">Perkiraan selesai: <b>{{ $l->estimasi_hari ? $l->estimasi_hari . ' hari kerja' : 'hari itu juga' }}</b></p></div>
</div>
@endsection
