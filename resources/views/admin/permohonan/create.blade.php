@extends('panel', ['judul' => 'Layanan loket', 'sub' => 'Buat permohonan untuk warga yang datang langsung ke kantor'])
@section('isi')
<form class="form" method="get" action="{{ route('admin.permohonan.create') }}">
  <div class="kartu" style="margin-top:0"><h2>1. Pilih jenis surat</h2>
    <div class="pilih-layanan">@foreach ($layanan as $l)<a href="{{ route('admin.permohonan.create', ['layanan' => $l->id, 'nik' => request('nik')]) }}" style="{{ $pilih?->id === $l->id ? 'border-color:var(--daun);background:var(--lime-2)' : '' }}"><span class="ikon">{!! ikon($l->ikon) !!}</span><b>{{ $l->nama }}</b></a>@endforeach</div>
  </div>
</form>
@if ($pilih)
<form class="form" method="post" action="{{ route('admin.permohonan.store') }}" novalidate>
  @csrf
  <input type="hidden" name="layanan_id" value="{{ $pilih->id }}">
  <div class="grid g2" style="align-items:start;margin-top:18px">
    <div class="kartu" style="margin-top:0"><h2>2. Cari penduduk</h2>
      <input type="hidden" name="penduduk_id" id="penduduk_id" value="{{ old('penduduk_id', $penduduk?->id) }}">
      <div id="terpilih" class="terpilih" @if (! old('penduduk_id', $penduduk?->id)) hidden @endif>{!! ikon('user') !!}<span id="terpilih-teks">@if ($penduduk)<b>{{ $penduduk->nama }}</b><br><span class="kecil">{{ $penduduk->nik }} - {{ $penduduk->alamat_lengkap }}</span>@endif</span></div>
      <div class="f" style="margin-top:12px"><label for="cari-pdd">Ketik NIK atau nama (min. 3 huruf)</label><input id="cari-pdd" autocomplete="off" placeholder="Contoh: 3273 atau SITI"><div id="hasil" class="hasil-cari" hidden></div>@error('penduduk_id')<div class="galat">{{ $message }}</div>@enderror</div>
      <p class="kecil muted">Warga belum terdaftar? <a href="{{ route('admin.penduduk.create') }}">Tambah data penduduk</a> dulu.</p>
    </div>
    <div class="kartu" style="margin-top:0"><h2>3. Isian {{ $pilih->nama }}</h2>
      @foreach ($pilih->isian as $f)
        <div class="f"><label for="d-{{ $f['nama'] }}">{{ $f['label'] }}@if ($f['wajib']) <span style="color:var(--merah)">*</span>@endif</label>
          @if ($f['tipe'] === 'textarea')<textarea id="d-{{ $f['nama'] }}" name="data[{{ $f['nama'] }}]">{{ old('data.' . $f['nama']) }}</textarea>@else<input id="d-{{ $f['nama'] }}" type="{{ $f['tipe'] }}" name="data[{{ $f['nama'] }}]" value="{{ old('data.' . $f['nama']) }}">@endif
          @error('data.' . $f['nama'])<div class="galat">{{ $message }}</div>@enderror</div>
      @endforeach
      <div class="f"><label for="keperluan">Keperluan <span style="color:var(--merah)">*</span></label><input id="keperluan" name="keperluan" value="{{ old('keperluan') }}">@error('keperluan')<div class="galat">{{ $message }}</div>@enderror</div>
      <div class="f"><label class="cek"><input type="checkbox" name="langsung_selesai" value="1" @checked(old('langsung_selesai', true))> Berkas asli sudah diperiksa, langsung terbitkan surat</label></div>
      <button class="btn">{!! ikon('cek') !!} Simpan permohonan</button>
    </div>
  </div>
</form>
@endif
@endsection
@push('skrip')
<script>
const inp = document.getElementById('cari-pdd'), hasil = document.getElementById('hasil');
let t;
if (inp) inp.addEventListener('input', () => {
  clearTimeout(t);
  const q = inp.value.trim();
  if (q.length < 3) { hasil.hidden = true; return; }
  t = setTimeout(async () => {
    const r = await fetch(@json(route('admin.permohonan.cari-penduduk')) + '?q=' + encodeURIComponent(q), { headers: { Accept: 'application/json' } });
    const d = await r.json();
    hasil.innerHTML = '';
    if (!d.length) { hasil.innerHTML = '<div style="padding:10px 12px" class="muted kecil">Tidak ditemukan</div>'; }
    d.forEach(p => {
      const b = document.createElement('button'); b.type = 'button';
      b.innerHTML = '<b></b><br><span class="kecil muted"></span>';
      b.querySelector('b').textContent = p.nama; b.querySelector('span').textContent = p.nik + ' - ' + p.alamat + ' RT ' + p.rt + '/RW ' + p.rw;
      b.onclick = () => { document.getElementById('penduduk_id').value = p.id; const tp = document.getElementById('terpilih'); tp.hidden = false; document.getElementById('terpilih-teks').innerHTML = b.innerHTML; hasil.hidden = true; inp.value = ''; };
      hasil.appendChild(b);
    });
    hasil.hidden = false;
  }, 250);
});
</script>
@endpush
