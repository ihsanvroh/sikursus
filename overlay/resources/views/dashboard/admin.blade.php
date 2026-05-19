@extends('layouts.app')
@section('title','Dashboard Admin')
@section('content')
<div class="row g-3 mb-3">
  <div class="col-md-3"><div class="card card-stat p-3"><small class="text-muted">Total Siswa Aktif</small><div class="stat-num text-primary">{{ $stats['total_siswa'] }}</div></div></div>
  <div class="col-md-3"><div class="card card-stat p-3"><small class="text-muted">Total Guru</small><div class="stat-num text-info">{{ $stats['total_guru'] }}</div></div></div>
  <div class="col-md-3"><div class="card card-stat p-3"><small class="text-muted">Kelas Aktif</small><div class="stat-num text-success">{{ $stats['total_kelas'] }}</div></div></div>
  <div class="col-md-3"><div class="card card-stat p-3"><small class="text-muted">Pendapatan Bulan Ini</small><div class="stat-num text-warning">Rp {{ number_format($stats['pendapatan_bulan_ini'],0,',','.') }}</div></div></div>
</div>

<div class="row g-3 mb-3">
  <div class="col-md-4"><div class="card card-stat p-3"><small class="text-muted">Tagihan Bulan Ini</small><div class="stat-num">{{ $stats['tagihan_bulan_ini'] }}</div></div></div>
  <div class="col-md-4"><div class="card card-stat p-3"><small class="text-muted">Belum Dibayar</small><div class="stat-num text-danger">{{ $stats['belum_bayar'] }}</div></div></div>
  <div class="col-md-4"><div class="card card-stat p-3"><small class="text-muted">Sudah Lunas</small><div class="stat-num text-success">{{ $stats['lunas'] }}</div></div></div>
</div>

<div class="card card-stat">
  <div class="card-body">
    <h6 class="mb-3">Pembayaran Terbaru</h6>
    <div class="table-responsive">
      <table class="table table-sm align-middle">
        <thead><tr><th>Tgl Bayar</th><th>Siswa</th><th>Kelas</th><th>Periode</th><th class="text-end">Nominal</th><th>Metode</th></tr></thead>
        <tbody>
        @forelse($recentBayar as $p)
          <tr>
            <td>{{ $p->tanggal_bayar->format('d M Y') }}</td>
            <td>{{ $p->tagihan->siswa->name ?? '-' }}</td>
            <td>{{ $p->tagihan->kelas->nama ?? '-' }}</td>
            <td>{{ $p->tagihan->periode ?? '-' }}</td>
            <td class="text-end">Rp {{ number_format($p->nominal,0,',','.') }}</td>
            <td><span class="badge bg-light text-dark">{{ ucfirst($p->metode) }}</span></td>
          </tr>
        @empty
          <tr><td colspan="6" class="text-center text-muted py-3">Belum ada pembayaran</td></tr>
        @endforelse
        </tbody>
      </table>
    </div>
  </div>
</div>
@endsection
