@extends('layouts.app')
@section('title','Tagihan')
@section('content')
<div class="card card-stat mb-3"><div class="card-body">
  <h6 class="mb-3">Generate Tagihan Bulanan</h6>
  <form method="POST" action="{{ route('tagihan.generate') }}" class="d-flex gap-2 flex-wrap" onsubmit="return confirm('Generate tagihan untuk semua enrollment aktif?')">
    @csrf
    <input type="month" name="periode" class="form-control" required style="max-width:200px;" value="{{ now()->format('Y-m') }}">
    <input type="date" name="tgl_jatuh_tempo" class="form-control" required style="max-width:200px;" value="{{ now()->day(10)->toDateString() }}">
    <button class="btn btn-success"><i class="bi bi-magic"></i> Generate</button>
    <a href="{{ route('tagihan.create') }}" class="btn btn-outline-primary"><i class="bi bi-plus-lg"></i> Tagihan Manual</a>
  </form>
  <small class="text-muted">Akan membuat tagihan untuk semua enrollment aktif yang belum punya tagihan di periode tersebut.</small>
</div></div>

<div class="card card-stat"><div class="card-body">
  <form method="GET" class="row g-2 mb-3">
    <div class="col-md-3 col-6">
      <input type="text" name="q" value="{{ request('q') }}" class="form-control" placeholder="Cari nama siswa">
    </div>
    <div class="col-md-2 col-6">
      <select name="status" class="form-select">
        <option value="">Semua Status</option>
        <option value="belum_bayar" @selected(request('status')=='belum_bayar')>Belum Bayar</option>
        <option value="sebagian" @selected(request('status')=='sebagian')>Sebagian</option>
        <option value="lunas" @selected(request('status')=='lunas')>Lunas</option>
      </select>
    </div>
    <div class="col-md-2 col-6">
      <select name="periode" class="form-select">
        <option value="">Semua Periode</option>
        @foreach($periodes as $p)
          <option value="{{ $p }}" @selected(request('periode')==$p)>{{ $p }}</option>
        @endforeach
      </select>
    </div>
    <div class="col-md-2 col-6">
      <select name="kelas_id" class="form-select">
        <option value="">Semua Kelas</option>
        @foreach(\App\Models\Kelas::orderBy('nama')->get() as $k)
          <option value="{{ $k->id }}" @selected(request('kelas_id')==$k->id)>{{ $k->nama }}</option>
        @endforeach
      </select>
    </div>
    <div class="col-md-3 col-12 d-flex gap-2 flex-wrap">
      <button class="btn btn-primary"><i class="bi bi-funnel"></i> Filter</button>
      @if(request()->hasAny(['q','status','periode','siswa_id','kelas_id']))<a href="{{ route('tagihan.index') }}" class="btn btn-outline-secondary">Reset</a>@endif
      <div class="ms-auto d-flex gap-1">
        <a href="{{ route('tagihan.export.pdf', request()->query()) }}" class="btn btn-outline-danger btn-sm" target="_blank" title="Export PDF">
          <i class="bi bi-file-earmark-pdf"></i>
        </a>
        <a href="{{ route('tagihan.export.excel', request()->query()) }}" class="btn btn-outline-success btn-sm" title="Export Excel">
          <i class="bi bi-file-earmark-excel"></i>
        </a>
      </div>
    </div>
  </form>

  <div class="table-responsive">
  <table class="table align-middle text-nowrap">
    <thead><tr><th>Periode</th><th>Siswa</th><th>Kelas</th><th>Nominal</th><th>Terbayar</th><th>Sisa</th><th>Jatuh Tempo</th><th>Status</th><th class="text-end">Aksi</th></tr></thead>
    <tbody>
    @forelse($tagihan as $t)
      <tr>
        <td>{{ $t->periode }}</td>
        <td><a href="{{ route('siswa.show',$t->siswa_id) }}">{{ $t->siswa->name }}</a></td>
        <td>{{ $t->kelas->nama }}</td>
        <td>Rp {{ number_format($t->nominal,0,',','.') }}</td>
        <td>Rp {{ number_format($t->terbayar,0,',','.') }}</td>
        <td>Rp {{ number_format($t->sisaBayar(),0,',','.') }}</td>
        <td>{{ $t->jatuh_tempo->format('d M Y') }}</td>
        <td><span class="badge-soft badge-{{ str_replace('_','-',$t->status) }}">{{ str_replace('_',' ',$t->status) }}</span></td>
        <td class="text-end">
          <a href="{{ route('tagihan.show',$t) }}" class="btn btn-sm btn-outline-primary" title="Lihat"><i class="bi bi-eye"></i></a>
          <a href="{{ route('tagihan.pdf',$t) }}" class="btn btn-sm btn-outline-danger" target="_blank" title="Download PDF"><i class="bi bi-file-earmark-pdf"></i></a>
          <form action="{{ route('tagihan.destroy',$t) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus tagihan?')">@csrf @method('DELETE')<button class="btn btn-sm btn-outline-danger" title="Hapus"><i class="bi bi-trash"></i></button></form>
        </td>
      </tr>
    @empty
      <tr><td colspan="9" class="text-center text-muted py-3">Tidak ada tagihan</td></tr>
    @endforelse
    </tbody>
  </table>
  </div>
  {{-- pagination --}}
  @include('partials._pagination', ['paginator' => $tagihan])
</div></div>
@endsection
