{{-- Pagination + per-page selector
     Usage: @include('partials._pagination', ['paginator' => $tagihan])
--}}
@if(isset($paginator) && method_exists($paginator, 'links'))
<div class="pagination-bar mt-3">
  <div class="d-flex align-items-center gap-2 flex-wrap">
    <small class="text-muted">
      Menampilkan {{ $paginator->firstItem() ?? 0 }}–{{ $paginator->lastItem() ?? 0 }}
      dari {{ $paginator->total() }} data
    </small>
    <form method="GET" class="d-inline-flex align-items-center gap-1">
      @foreach(request()->except(['per_page','page']) as $k => $v)
        @if(is_array($v))
          @foreach($v as $vv)<input type="hidden" name="{{ $k }}[]" value="{{ $vv }}">@endforeach
        @else
          <input type="hidden" name="{{ $k }}" value="{{ $v }}">
        @endif
      @endforeach
      <label class="small text-muted mb-0">Per halaman</label>
      <select name="per_page" class="form-select form-select-sm" style="width:auto" onchange="this.form.submit()">
        @foreach([10,25,50,100] as $n)
          <option value="{{ $n }}" {{ (int)request('per_page', $paginator->perPage()) === $n ? 'selected' : '' }}>{{ $n }}</option>
        @endforeach
      </select>
    </form>
  </div>
  <div>
    {{ $paginator->onEachSide(1)->links('pagination::bootstrap-5') }}
  </div>
</div>
@endif
