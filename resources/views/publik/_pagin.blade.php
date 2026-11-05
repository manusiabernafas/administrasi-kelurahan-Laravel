@if ($p->lastPage() > 1)
<nav class="pagin" aria-label="Halaman">
  @for ($i = 1; $i <= $p->lastPage(); $i++)
    @if ($i === $p->currentPage())<span class="on" aria-current="page">{{ $i }}</span>@else<a href="{{ $p->url($i) }}">{{ $i }}</a>@endif
  @endfor
</nav>
@endif
