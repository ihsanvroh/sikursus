@extends('layouts.app')
@section('title','Laporan Bulanan')
@section('content')
<div class="card card-stat mb-3"><div class="card-body">
  <form method="GET" class="d-flex gap-2 flex-wrap align-items-end">
    <div><label class="form-label small">Bulan</label><input type="month" name="bulan" class="form-control" value="{{ $bulan }}"></div>
    <div><label class="form-label small">Filter Harian</label><input type="date" name="tanggal" class="form-control" value="{{ $tanggal }}"></div>
    <button class="btn btn-primary"><i class="bi bi-funnel"></i> Tampilkan</button>
    @if($tanggal)<a href="?bulan={{ $bulan }}" class="btn btn-outline-secondary">Hapus filter harian</a>@endif
  </form>
</div></div>

<div class="row g-3 mb-3">
  <div class="col-md-4"><div class="card card-stat p-3"><small class="text-muted">Total Pembayaran</small><div class="stat-num text-primary">{{ $bayar->count() }}</div></div></div>
  <div class="col-md-4"><div class="card card-stat p-3"><small class="text-muted">Total Pendapatan</small><div class="stat-num text-success">Rp {{ number_format($total,0,',','.') }}</div></div></div>
  <div class="col-md-4"><div class="card card-stat p-3"><small class="text-muted">Tagihan Belum Lunas</small><div class="stat-num text-danger">{{ $outstanding->count() }}</div></div></div>
</div>

<div class="row g-3">
  <div class="col-md-6">
    <div class="card card-stat"><div class="card-body">
      <h6>Per Hari</h6>
      <table class="table table-sm">
        <thead><tr><th>Tanggal</th><th class="text-end">Tx</th><th class="text-end">Total</th></tr></thead>
        <tbody>
          @forelse($perHari as $tgl => $d)
            <tr><td>{{ \Carbon\Carbon::parse($tgl)->format('d M Y') }}</td><td class="text-end">{{ $d['count'] }}</td><td class="text-end">Rp {{ number_format($d['total'],0,',','.') }}</td></tr>
          @empty
            <tr><td colspan="3" class="text-muted text-center">-</td></tr>
          @endforelse
        </tbody>
      </table>
    </div></div>
  </div>
  <div class="col-md-6">
    <div class="card card-stat"><div class="card-body">
      <h6>Per Kelas</h6>
      <table class="table table-sm">
        <thead><tr><th>Kelas</th><th class="text-end">Tx</th><th class="text-end">Total</th></tr></thead>
        <tbody>
          @forelse($perKelas as $nama => $d)
            <tr><td>{{ $nama }}</td><td class="text-end">{{ $d['count'] }}</td><td class="text-end">Rp {{ number_format($d['total'],0,',','.') }}</td></tr>
          @empty
            <tr><td colspan="3" class="text-muted text-center">-</td></tr>
          @endforelse
        </tbody>
      </table>
    </div></div>
  </div>
</div>

<div class="card card-stat mt-3"><div class="card-body">
  <h6>Detail Pembayaran</h6>
  <table class="table table-sm">
    <thead><tr><th>Tanggal</th><th>Siswa</th><th>Kelas</th><th>Periode</th><th>Nominal</th><th>Metode</th><th>Admin</th></tr></thead>
    <tbody>
      @forelse($bayar as $p)
        <tr>
          <td>{{ $p->tanggal_bayar->format('d M Y') }}</td>
          <td>{{ $p->tagihan->siswa->name ?? '-' }}</td>
          <td>{{ $p->tagihan->kelas->nama ?? '-' }}</td>
          <td>{{ $p->tagihan->periode ?? '-' }}</td>
          <td>Rp {{ number_format($p->nominal,0,',','.') }}</td>
          <td>{{ ucfirst($p->metode) }}</td>
          <td>{{ $p->admin->name ?? '-' }}</td>
        </tr>
      @empty
        <tr><td colspan="7" class="text-center text-muted py-3">Tidak ada data</td></tr>
      @endforelse
    </tbody>
  </table>
</div></div>

@if($outstanding->count() > 0)
<div class="card card-stat mt-3"><div class="card-body">
  <h6>Tagihan Belum Lunas Periode {{ $bulan }}</h6>
  <table class="table table-sm">
    <thead><tr><th>Siswa</th><th>Kelas</th><th>Nominal</th><th>Terbayar</th><th>Sisa</th><th>Jatuh Tempo</th></tr></thead>
    <tbody>
      @foreach($outstanding as $t)
        <tr>
          <td>{{ $t->siswa->name }}</td>
          <td>{{ $t->kelas->nama }}</td>
          <td>Rp {{ number_format($t->nominal,0,',','.') }}</td>
          <td>Rp {{ number_format($t->terbayar,0,',','.') }}</td>
          <td class="text-danger">Rp {{ number_format($t->sisaBayar(),0,',','.') }}</td>
          <td>{{ $t->jatuh_tempo->format('d M Y') }}</td>
        </tr>
      @endforeach
    </tbody>
  </table>
</div></div>
@endif
@endsection
