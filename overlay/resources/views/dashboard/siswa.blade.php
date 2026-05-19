@extends('layouts.app')
@section('title','Dashboard Siswa')
@section('content')
<div class="alert alert-info">Halo <strong>{{ auth()->user()->name }}</strong>, kamu memiliki <strong>{{ $belum }}</strong> tagihan yang belum lunas.</div>
<div class="card card-stat">
  <div class="card-body">
    <h6 class="mb-3">Tagihan Saya</h6>
    <table class="table">
      <thead><tr><th>Periode</th><th>Kelas</th><th>Nominal</th><th>Terbayar</th><th>Jatuh Tempo</th><th>Status</th></tr></thead>
      <tbody>
      @forelse($tagihan as $t)
        <tr>
          <td>{{ $t->periode }}</td>
          <td>{{ $t->kelas->nama }}</td>
          <td>Rp {{ number_format($t->nominal,0,',','.') }}</td>
          <td>Rp {{ number_format($t->terbayar,0,',','.') }}</td>
          <td>{{ $t->jatuh_tempo->format('d M Y') }}</td>
          <td><span class="badge-soft badge-{{ str_replace('_','-',$t->status) }}">{{ str_replace('_',' ',$t->status) }}</span></td>
        </tr>
      @empty
        <tr><td colspan="6" class="text-center text-muted py-3">Belum ada tagihan</td></tr>
      @endforelse
      </tbody>
    </table>
  </div>
</div>
@endsection
