@php $baru = ! $l->exists; @endphp
@extends('panel', ['judul' => $baru ? 'Tambah layanan' : 'Ubah ' . $l->nama])
@section('aksi')<a class="btn garis" href="{{ route('admin.layanan.index') }}">{!! ikon('kiri') !!} Kembali</a>@endsection
@section('isi')
@php $v = fn ($k) => old($k, $l->$k); @endphp
<form class="form" method="post" action="{{ $baru ? route('admin.layanan.store') : route('admin.layanan.update', $l) }}" novalidate>
  @csrf @unless ($baru) @method('PUT') @endunless
  <div class="grid g3" style="align-items:start">
    <div class="kartu lebar-2" style="margin-top:0">
      <h2>Informasi layanan</h2>
      <div class="tiga">
        <div class="f"><label for="kode">Kode surat</label><input id="kode" name="kode" value="{{ $v('kode') }}" maxlength="10" placeholder="SKD"><div class="hint">Dipakai di nomor surat.</div>@error('kode')<div class="galat">{{ $message }}</div>@enderror</div>
        <div class="f"><label for="estimasi_hari">Estimasi (hari kerja)</label><input id="estimasi_hari" type="number" name="estimasi_hari" value="{{ $v('estimasi_hari') }}" min="0" max="30"><div class="hint">0 = selesai hari itu juga.</div>@error('estimasi_hari')<div class="galat">{{ $message }}</div>@enderror</div>
        <div class="f"><label for="urutan">Urutan tampil</label><input id="urutan" type="number" name="urutan" value="{{ $v('urutan') }}">@error('urutan')<div class="galat">{{ $message }}</div>@enderror</div>
      </div>
      <div class="f"><label for="nama">Nama layanan</label><input id="nama" name="nama" value="{{ $v('nama') }}" placeholder="Surat Keterangan Domisili">@error('nama')<div class="galat">{{ $message }}</div>@enderror</div>
      <div class="f"><label for="ringkas">Penjelasan singkat</label><input id="ringkas" name="ringkas" value="{{ $v('ringkas') }}" maxlength="255">@error('ringkas')<div class="galat">{{ $message }}</div>@enderror</div>
      <div class="f"><label for="persyaratan">Persyaratan (satu baris satu syarat)</label><textarea id="persyaratan" name="persyaratan">{{ $v('persyaratan') }}</textarea>@error('persyaratan')<div class="galat">{{ $message }}</div>@enderror</div>
      <div class="f"><label for="kolom">Isian tambahan untuk warga (opsional)</label><textarea id="kolom" name="kolom" style="font-family:monospace;font-size:13.5px" placeholder="nama_usaha|Nama usaha|wajib">{{ $v('kolom') }}</textarea><div class="hint">Format per baris: <code>nama_kolom|Label|wajib|tipe</code>. Tipe: text, date, number, textarea. Nama kolom bisa dipakai di isi surat sebagai <code>{nama_kolom}</code>.</div>@error('kolom')<div class="galat">{{ $message }}</div>@enderror</div>
      <div class="f"><label for="isi_surat">Isi surat</label><textarea id="isi_surat" name="isi_surat" style="min-height:180px">{{ $v('isi_surat') }}</textarea>@error('isi_surat')<div class="galat">{{ $message }}</div>@enderror</div>
    </div>
    <div>
      <div class="kartu" style="margin-top:0"><h2>Tampilan</h2>
        <div class="f"><label>Ikon</label><div style="display:flex;flex-wrap:wrap;gap:8px">@foreach (\App\Http\Controllers\Admin\LayananController::IKON as $ic)<label class="cek" style="border:1.5px solid var(--garis);border-radius:12px;padding:8px"><input type="radio" name="ikon" value="{{ $ic }}" @checked($v('ikon') === $ic)>{!! ikon($ic) !!}</label>@endforeach</div>@error('ikon')<div class="galat">{{ $message }}</div>@enderror</div>
        <div class="f"><label class="cek"><input type="checkbox" name="aktif" value="1" @checked(old('aktif', $l->aktif))> Aktif (tampil dan bisa diajukan)</label></div>
        <button class="btn" style="width:100%">{!! ikon('cek') !!} Simpan layanan</button>
      </div>
      <div class="kartu bantuan"><b>Kode isi surat yang bisa dipakai</b><br><code>{nama}</code> <code>{nik}</code> <code>{no_kk}</code> <code>{ttl}</code> <code>{jenis_kelamin}</code> <code>{agama}</code> <code>{pekerjaan}</code> <code>{status_perkawinan}</code> <code>{alamat}</code> <code>{rt}</code> <code>{rw}</code> <code>{keperluan}</code> <code>{kelurahan}</code> <code>{kecamatan}</code> <code>{kota}</code> <code>{tanggal}</code>, ditambah nama kolom isian tambahan. Pisahkan paragraf dengan baris baru.</div>
    </div>
  </div>
</form>
@endsection
