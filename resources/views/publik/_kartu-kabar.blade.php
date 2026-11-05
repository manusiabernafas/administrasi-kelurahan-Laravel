<a class="kartu-kabar" href="{{ route('kabar.detail', $x->slug) }}">
  <span class="gbr">@if ($x->gambar)<img src="{{ asset('uploads/' . $x->gambar) }}" alt="" loading="lazy">@else{!! ikon('toa') !!}@endif</span>
  <span class="meta"><b>{{ $x->kategori }}</b><span>{{ tgl($x->terbit_at) }}</span></span>
  <h3>{{ $x->judul }}</h3>
</a>
