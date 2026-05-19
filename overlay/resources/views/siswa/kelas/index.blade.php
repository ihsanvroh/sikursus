@extends('layouts.app')
@section('title','Kelas yang Saya Ikuti')
@section('content')

@if($enrollments->isEmpty())
  <div class="card card-stat"><div class="card-body text-center py-5">
    <i class="bi bi-easel display-4 text-muted d-block mb-2"></i>
    <p class="text-muted">Belum terdaftar di kelas. Hubungi admin untuk pendaftaran.</p>
  </div></div>
@else
  <div class="row g-3">
    @foreach($enrollments as $e)
      <div class="col-md-6 col-lg-4">
        <div class="card card-stat h-100">
          <div class="card-body">
            <div class="d-flex align-items-start">
              <div>
                <h6 class="mb-1">{{ $e->kelas->nama }}</h6>
                <code>{{ $e->kelas->kode }}</code>
              </div>
              <span class="badge-soft badge-{{ $e->status }} ms-auto">{{ ucfirst($e->status) }}</span>
            </div>

            <table class="table table-sm mt-3 mb-0">
              <tr><th class="ps-0">Guru</th><td>{{ $e->kelas->guru->name ?? '-' }}</td></tr>
              <tr><th class="ps-0">Tarif</th><td>Rp {{ number_format($e->kelas->tarif,0,',','.') }}</td></tr>
              <tr><th class="ps-0">Mulai</th><td>{{ \Carbon\Carbon::parse($e->tgl_mulai)->format('d M Y') }}</td></tr>
              @if($e->tgl_selesai)
                <tr><th class="ps-0">Selesai</th><td>{{ \Carbon\Carbon::parse($e->tgl_selesai)->format('d M Y') }}</td></tr>
              @endif
            </table>

            <div class="mt-3">
              <small class="text-muted text-uppercase d-block mb-1" style="font-size:.7rem;">Jadwal</small>
              @forelse($e->kelas->jadwal as $j)
                <span class="badge bg-light text-dark border me-1">{{ $j->label() }}</span>
              @empty
                <small class="text-muted fst-italic">Belum ada jadwal</small>
              @endforelse
            </div>
          </div>
        </div>
      </div>
    @endforeach
  </div>
@endif
@endsection
