@extends('layouts.app')
@section('title','Detail Kelas: '.$kelas->nama)
@section('content')
<div class="mb-3">@include('partials._back', ['route' => route('kelas.index')])</div>
<div class="row g-3">
  <div class="col-md-5">
    <div class="card card-stat">
      <div class="card-body">
        <h5>{{ $kelas->nama }}</h5>
        <code>{{ $kelas->kode }}</code>
        <table class="table table-sm mt-3">
          <tr><th>Tarif</th><td>Rp {{ number_format($kelas->tarif,0,',','.') }}</td></tr>
          <tr><th>Guru</th><td>{{ $kelas->guru->name ?? '-' }}</td></tr>
          <tr><th>Status</th><td>@if($kelas->aktif)<span class="badge bg-success">Aktif</span>@else<span class="badge bg-secondary">Non-aktif</span>@endif</td></tr>
        </table>
        @if($kelas->deskripsi)<div class="alert alert-light small">{{ $kelas->deskripsi }}</div>@endif
      </div>
    </div>
  </div>
  <div class="col-md-7">
    <div class="card card-stat">
      <div class="card-body">
        <h6 class="mb-3">Siswa Aktif ({{ $kelas->siswa->count() }})</h6>
        <table class="table table-sm">
          <thead><tr><th>Nama</th><th>Email</th><th>Tgl Mulai</th></tr></thead>
          <tbody>
            @forelse($kelas->siswa as $s)
              <tr><td><a href="{{ route('siswa.show',$s) }}">{{ $s->name }}</a></td><td>{{ $s->email }}</td><td>{{ \Carbon\Carbon::parse($s->pivot->tgl_mulai)->format('d M Y') }}</td></tr>
            @empty
              <tr><td colspan="3" class="text-center text-muted py-3">Belum ada siswa</td></tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>
@endsection
