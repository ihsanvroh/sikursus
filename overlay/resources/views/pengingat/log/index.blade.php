@extends('layouts.app')
@section('title','Log Pengingat')
@section('content')
<div class="card card-stat"><div class="card-body">
  <form method="GET" class="d-flex gap-2 flex-wrap mb-3">
    <input type="text" name="q" value="{{ request('q') }}" class="form-control" placeholder="Cari nama siswa" style="max-width:240px;">
    <select name="mode" class="form-select" style="max-width:160px;">
      <option value="">Semua Mode</option>
      <option value="manual" @selected(request('mode')=='manual')>Manual</option>
      <option value="auto" @selected(request('mode')=='auto')>Otomatis</option>
    </select>
    <select name="status" class="form-select" style="max-width:160px;">
      <option value="">Semua Status</option>
      <option value="terkirim" @selected(request('status')=='terkirim')>Terkirim</option>
      <option value="gagal" @selected(request('status')=='gagal')>Gagal</option>
    </select>
    <button class="btn btn-primary"><i class="bi bi-funnel"></i> Filter</button>
    @if(request()->hasAny(['q','mode','status']))<a href="{{ route('pengingat.log.index') }}" class="btn btn-outline-secondary">Reset</a>@endif
  </form>

  <table class="table align-middle">
    <thead><tr><th>Waktu</th><th>Siswa</th><th>Tagihan</th><th>Mode</th><th>Judul</th><th>Status</th></tr></thead>
    <tbody>
    @forelse($logs as $l)
      <tr>
        <td>{{ $l->sent_at?->format('d M Y H:i') }}</td>
        <td>{{ $l->siswa->name ?? '-' }}</td>
        <td>@if($l->tagihan){{ $l->tagihan->kelas->nama ?? '-' }} ({{ $l->tagihan->periode }})@else <span class="text-muted">-</span>@endif</td>
        <td><span class="badge bg-{{ $l->mode == 'auto' ? 'info' : 'secondary' }}">{{ ucfirst($l->mode) }}</span></td>
        <td><small>{{ \Illuminate\Support\Str::limit($l->judul, 60) }}</small></td>
        <td>@if($l->status == 'terkirim')<span class="badge bg-success">Terkirim</span>@else<span class="badge bg-danger" title="{{ $l->error }}">Gagal</span>@endif</td>
      </tr>
    @empty
      <tr><td colspan="6" class="text-center text-muted py-3">Belum ada log</td></tr>
    @endforelse
    </tbody>
  </table>
  @include('partials._pagination', ['paginator' => $logs])
</div></div>
@endsection
