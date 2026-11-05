@extends('panel', ['judul' => 'Kabar & pengumuman', 'sub' => 'Tampil di halaman depan situs kelurahan'])
@section('aksi')<a class="btn lime" href="{{ route('admin.kabar.create') }}">{!! ikon('plus') !!} Tulis kabar</a>@endsection
@section('isi')
<div class="kartu">@if ($daftar->isEmpty())<div class="kosong">{!! ikon('toa') !!}<p>Belum ada kabar.</p></div>@else
<div class="tabel-wrap"><table class="tabel"><thead><tr><th>Judul</th><th class="hp-sembunyi">Kategori</th><th class="hp-sembunyi">Terbit</th><th class="hp-sembunyi">Dibaca</th><th>Status</th><th></th></tr></thead><tbody>
  @foreach ($daftar as $k)<tr><td><b>{{ $k->judul }}</b></td><td class="hp-sembunyi">{{ $k->kategori }}</td><td class="hp-sembunyi kecil">{{ tgl($k->terbit_at) }}</td><td class="hp-sembunyi">{{ $k->dilihat }}</td><td>{!! $k->terbit ? ($k->terbit_at > now() ? lencana('Terjadwal', 'kuning') : lencana('Tayang', 'hijau')) : lencana('Draf', 'abu') !!}</td>
    <td class="aksi">@if ($k->terbit && $k->terbit_at <= now())<a class="btn garis kecil hp-sembunyi" href="{{ route('kabar.detail', $k->slug) }}" target="_blank">{!! ikon('mata') !!}</a>@endif <a class="btn garis kecil" href="{{ route('admin.kabar.edit', $k) }}">{!! ikon('edit') !!}</a> <form method="post" action="{{ route('admin.kabar.destroy', $k) }}" data-yakin="Hapus kabar ini?">@csrf @method('DELETE')<button class="btn merah kecil" aria-label="Hapus">{!! ikon('hapus') !!}</button></form></td></tr>@endforeach
</tbody></table></div>@include('publik._pagin', ['p' => $daftar])@endif</div>
@endsection
