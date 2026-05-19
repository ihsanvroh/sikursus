@extends('layouts.app')
@section('title','Dashboard Guru')
@section('content')
<div class="row g-3 mb-3">
  <div class="col-md-3"><div class="card card-stat p-3"><small class="text-muted">Kelas Diampu</small><div class="stat-num text-primary">{{ $stats['kelas_diampu'] }}</div></div></div>
  <div class="col-md-3"><div class="card card-stat p-3"><small class="text-muted">Total Siswa</small><div class="stat-num text-info">{{ $stats['siswa'] }}</div></div></div>
</div>
<div class="card card-stat">
  <div class="card-body">
    <h6 class="mb-3">Kelas Saya</h6>
    <table class="table">
      <thead><tr><th>Kode</th><th>Nama Kelas</th><th>Tarif</th><th>Siswa Aktif</th></tr></thead>
      <tbody>
        @forelse($kelas as $k)
          <tr>
            <td><code>{{ $k->kode }}</code></td>
            <td>{{ $k->nama }}</td>
            <td>Rp {{ number_format($k->tarif,0,',','.') }}</td>
            <td>{{ $k->siswa_aktif_count }}</td>
          </tr>
        @empty
          <tr><td colspan="4" class="text-center text-muted py-3">Belum ada kelas yang diampu</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
</div>
@endsection
