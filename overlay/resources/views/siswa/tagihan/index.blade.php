@extends('layouts.app')
@section('title','Tagihan Saya')
@section('content')
<div class="card card-stat"><div class="card-body">
  <form method="GET" class="row g-2 mb-3">
    <div class="col-md-3 col-6">
      <select name="status" class="form-select">
        <option value="">Semua Status</option>
        <option value="belum_bayar" @selected(request('status')=='belum_bayar')>Belum Bayar</option>
        <option value="sebagian" @selected(request('status')=='sebagian')>Sebagian</option>
        <option value="lunas" @selected(request('status')=='lunas')>Lunas</option>
      </select>
    </div>
    <div class="col-md-3 col-6">
      <select name="periode" class="form-select">
        <option value="">Semua Periode</option>
        @foreach($periodes as $p)
          <option value="{{ $p }}" @selected(request('periode')==$p)>{{ $p }}</option>
        @endforeach
      </select>
    </div>
    <div class="col-md-3 col-6">
      <select name="kelas_id" class="form-select">
        <option value="">Semua Kelas</option>
        @foreach($kelasList as $k)
          <option value="{{ $k->id }}" @selected(request('kelas_id')==$k->id)>{{ $k->nama }}</option>
        @endforeach
      </select>
    </div>
    <div class="col-md-3 col-6 d-flex gap-2">
      <button class="btn btn-primary"><i class="bi bi-funnel"></i> Filter</button>
      @if(request()->hasAny(['status','periode','kelas_id']))<a href="{{ route('siswa.tagihan.index') }}" class="btn btn-outline-secondary">Reset</a>@endif
    </div>
  </form>

  <div class="table-responsive">
  <table class="table align-middle text-nowrap">
    <thead>
      <tr><th>Periode</th><th>Kelas</th><th>Nominal</th><th>Terbayar</th><th>Sisa</th><th>Jatuh Tempo</th><th>Status</th><th class="text-end">Aksi</th></tr>
    </thead>
    <tbody>
      @forelse($tagihan as $t)
        <tr>
          <td>{{ $t->periode }}</td>
          <td>{{ $t->kelas->nama ?? '-' }}</td>
          <td>Rp {{ number_format($t->nominal,0,',','.') }}</td>
          <td>Rp {{ number_format($t->terbayar,0,',','.') }}</td>
          <td>Rp {{ number_format($t->sisaBayar(),0,',','.') }}</td>
          <td>{{ $t->jatuh_tempo->format('d M Y') }}</td>
          <td><span class="badge-soft badge-{{ str_replace('_','-',$t->status) }}">{{ str_replace('_',' ',$t->status) }}</span></td>
          <td class="text-end">
            <a href="{{ route('siswa.tagihan.pdf',$t) }}" class="btn btn-sm btn-outline-danger" target="_blank" title="Download PDF">
              <i class="bi bi-file-earmark-pdf"></i> PDF
            </a>
          </td>
        </tr>
      @empty
        <tr><td colspan="8" class="text-center text-muted py-3">Tidak ada tagihan</td></tr>
      @endforelse
    </tbody>
  </table>
  </div>
  @include('partials._pagination', ['paginator' => $tagihan])
</div></div>
@endsection
