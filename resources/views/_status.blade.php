@php $urut = ['diajukan', 'diverifikasi', 'diproses', 'selesai']; $pos = array_search($p->status, $urut); @endphp
<div class="langkah-status {{ $p->status === 'ditolak' ? 'tolak' : '' }}">
  @foreach ($urut as $i => $s)
    <div class="{{ $p->status === 'ditolak' ? ($i === 0 ? 'on' : '') : ($pos !== false && $i <= $pos ? 'on' : '') }}"><i></i>{{ $p->status === 'ditolak' && $i === 3 ? 'Ditolak' : \App\Models\Permohonan::STATUS[$s][0] }}</div>
  @endforeach
</div>
