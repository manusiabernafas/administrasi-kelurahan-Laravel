@extends('panel', ['judul' => $p->nama, 'sub' => 'NIK ' . $p->nik])
@section('aksi')<a class="btn garis" href="{{ route('admin.penduduk.index') }}">{!! ikon('kiri') !!} Daftar</a><a class="btn garis" href="{{ route('admin.penduduk.edit', $p) }}">{!! ikon('edit') !!} Ubah</a><a class="btn lime" href="{{ route('admin.permohonan.create', ['nik' => $p->nik]) }}">{!! ikon('plus') !!} Buat surat</a>@endsection
@section('isi')
<div class="grid g2" style="align-items:start">
  <div class="kartu" style="margin-top:0"><h2>Biodata</h2>
    <dl class="dl"><dt>No. KK</dt><dd>{{ $p->no_kk }}</dd><dt>Tempat, tgl lahir</dt><dd>{{ $p->ttl }} ({{ $p->umur }} tahun)</dd><dt>Jenis kelamin</dt><dd>{{ $p->jenis_kelamin === 'L' ? 'Laki-laki' : 'Perempuan' }}</dd><dt>Agama</dt><dd>{{ $p->agama }}</dd><dt>Status kawin</dt><dd>{{ $p->status_perkawinan }}</dd><dt>Pendidikan</dt><dd>{{ $p->pendidikan ?: '-' }}</dd><dt>Pekerjaan</dt><dd>{{ $p->pekerjaan ?: '-' }}</dd><dt>Alamat</dt><dd>{{ $p->alamat_lengkap }}</dd><dt>Status</dt><dd>{!! lencana(ucfirst($p->status), $p->status === 'aktif' ? 'hijau' : 'abu') !!}</dd><dt>Akun warga</dt><dd>{{ \App\Models\User::where('nik', $p->nik)->exists() ? 'Sudah punya akun' : 'Belum mendaftar' }}</dd></dl>
  </div>
  <div>
    <div class="kartu" style="margin-top:0"><h2>Anggota keluarga (KK sama)</h2>@forelse ($keluarga as $x)<div style="display:flex;justify-content:space-between;padding:8px 0;border-top:1px solid var(--garis)"><a href="{{ route('admin.penduduk.show', $x) }}">{{ $x->nama }}</a><span class="kecil muted">{{ $x->umur }} th, {{ $x->jenis_kelamin }}</span></div>@empty<p class="muted" style="margin:0">Tidak ada anggota lain.</p>@endforelse</div>
    <div class="kartu"><h2>Riwayat surat</h2>@forelse ($riwayat as $x)<div style="display:flex;justify-content:space-between;gap:10px;padding:8px 0;border-top:1px solid var(--garis)"><a href="{{ route('admin.permohonan.show', $x) }}">{{ $x->layanan->nama }}</a><span>{!! lencana($x->label_status, $x->warna_status) !!}</span></div>@empty<p class="muted" style="margin:0">Belum pernah mengurus surat.</p>@endforelse</div>
  </div>
</div>
@endsection
