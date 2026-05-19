@extends('layouts.app')
@section('title','Kelas yang Saya Ampu')
@section('content')

@if($kelas->isEmpty())
  <div class="card card-stat"><div class="card-body text-center py-5">
    <i class="bi bi-easel display-4 text-muted d-block mb-2"></i>
    <p class="text-muted">Belum ada kelas yang diserahkan ke Anda. Hubungi admin.</p>
  </div></div>
@else
  <div class="row g-3">
    @foreach($kelas as $k)
      <div class="col-md-6 col-lg-4">
        <div class="card card-stat h-100">
          <div class="card-body">
            <div class="d-flex align-items-start">
              <div>
                <h6 class="mb-1">{{ $k->nama }}</h6>
                <code>{{ $k->kode }}</code>
              </div>
              <span class="badge bg-info ms-auto">{{ $k->siswa_aktif_count }} siswa</span>
            </div>
            @if($k->deskripsi)<small class="text-muted d-block mt-2">{{ $k->deskripsi }}</small>@endif

            <div class="mt-3">
              <small class="text-muted text-uppercase d-block mb-1" style="font-size:.7rem;">Jadwal</small>
              @forelse($k->jadwal as $j)
                <span class="badge bg-light text-dark border me-1">{{ $j->label() }}</span>
              @empty
                <small class="text-muted fst-italic">Belum ada jadwal</small>
              @endforelse
            </div>

            <div class="mt-3 d-flex gap-2">
              <a href="{{ route('guru.kelas.show', $k) }}" class="btn btn-sm btn-outline-primary"><i class="bi bi-eye me-1"></i>Detail</a>
              <a href="{{ route('guru.pesan.form', ['kelas_id' => $k->id]) }}" class="btn btn-sm btn-outline-success"><i class="bi bi-send me-1"></i>Kirim Pesan</a>
            </div>
          </div>
        </div>
      </div>
    @endforeach
  </div>
@endif
@endsection
