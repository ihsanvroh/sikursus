@extends('layouts.app')
@section('title','Detail Kelas: '.$kelas->nama)
@section('content')
<div class="mb-3 d-flex gap-2 flex-wrap">
  @include('partials._back', ['route' => route('guru.kelas.index')])
  <a href="{{ route('guru.pesan.form', ['kelas_id' => $kelas->id]) }}" class="btn btn-outline-success btn-sm">
    <i class="bi bi-send me-1"></i>Kirim Pesan ke Siswa Kelas Ini
  </a>
</div>

<div class="row g-3">
  <div class="col-md-5">
    <div class="card card-stat"><div class="card-body">
      <h5>{{ $kelas->nama }}</h5>
      <code>{{ $kelas->kode }}</code>
      <table class="table table-sm mt-3">
        <tr><th>Tarif</th><td>Rp {{ number_format($kelas->tarif,0,',','.') }}</td></tr>
        <tr><th>Status</th><td>@if($kelas->aktif)<span class="badge bg-success">Aktif</span>@else<span class="badge bg-secondary">Non-aktif</span>@endif</td></tr>
      </table>
      @if($kelas->deskripsi)<div class="alert alert-light small">{{ $kelas->deskripsi }}</div>@endif

      <h6 class="mt-3 mb-2"><i class="bi bi-calendar-week"></i> Jadwal Rutin</h6>
      @if($kelas->jadwal->isEmpty())
        <small class="text-muted fst-italic">Belum ada jadwal.</small>
      @else
        <ul class="list-unstyled mb-0">
          @foreach($kelas->jadwal as $j)
            <li><i class="bi bi-clock me-1 text-primary"></i>{{ $j->label() }}</li>
          @endforeach
        </ul>
      @endif
    </div></div>
  </div>
  <div class="col-md-7">
    <div class="card card-stat"><div class="card-body">
      <h6 class="mb-3">Siswa Aktif <span class="badge bg-info">{{ $kelas->siswa->count() }}</span></h6>
      <div class="table-responsive">
        <table class="table table-sm align-middle">
          <thead><tr><th>Nama</th><th>Email</th><th>No HP</th><th>Tgl Mulai</th></tr></thead>
          <tbody>
            @forelse($kelas->siswa as $s)
              <tr>
                <td><strong>{{ $s->name }}</strong></td>
                <td>{{ $s->email }}</td>
                <td>{{ $s->nohp ?? '-' }}</td>
                <td>{{ \Carbon\Carbon::parse($s->pivot->tgl_mulai)->format('d M Y') }}</td>
              </tr>
            @empty
              <tr><td colspan="4" class="text-center text-muted py-3">Belum ada siswa</td></tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div></div>
  </div>
</div>
@endsection
