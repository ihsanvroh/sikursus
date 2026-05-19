{{-- Tombol Kembali — pakai history back kalau ada referer, fallback ke route yg dikasih
     Usage: @include('partials._back', ['route' => route('siswa.index'), 'label' => 'Kembali'])
--}}
@php
  $fallback = $route ?? url()->previous();
  $label = $label ?? 'Kembali';
@endphp
<a href="{{ $fallback }}" class="btn btn-outline-secondary btn-sm" onclick="if(document.referrer && history.length>1){event.preventDefault();history.back();}">
  <i class="bi bi-arrow-left me-1"></i>{{ $label }}
</a>
