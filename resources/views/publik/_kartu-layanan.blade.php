<a class="kartu-layanan" href="{{ route('layanan.detail', strtolower($l->kode)) }}">
  <span class="pola">{!! ikon($l->ikon) !!}</span>
  <div class="atas-k"><span class="label">{{ $l->estimasi_hari ? 'Selesai ' . $l->estimasi_hari . ' hari kerja' : 'Selesai hari itu juga' }}</span><span class="ikon">{!! ikon($l->ikon) !!}</span></div>
  <h3>{{ $l->nama }}</h3>
  <p>{{ $l->ringkas }}</p>
  <span class="btn putih">Lihat syarat <span class="bulat">{!! ikon('panah') !!}</span></span>
</a>
