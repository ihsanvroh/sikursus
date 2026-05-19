@extends('layouts.app')
@section('title','Siswa yang Saya Ajar')
@section('content')
<div class="card card-stat"><div class="card-body">
  <form method="GET" class="d-flex gap-2 flex-wrap mb-3">
    <input type="text" name="q" value="{{ request('q') }}" class="form-control" placeholder="Cari nama/email" style="max-width:240px;">
    <select name="kelas_id" class="form-select" style="max-width:240px;">
      <option value="">Semua kelas saya</option>
      @foreach($kelasList as $k)
        <option value="{{ $k->id }}" @selected(request('kelas_id')==$k->id)>{{ $k->nama }}</option>
      @endforeach
    </select>
    <button class="btn btn-primary"><i class="bi bi-funnel"></i> Filter</button>
    @if(request()->hasAny(['q','kelas_id']))<a href="{{ route('guru.siswa.index') }}" class="btn btn-outline-secondary">Reset</a>@endif
  </form>

  <div class="table-responsive">
  <table class="table align-middle text-nowrap">
    <thead><tr><th>#</th><th>Nama</th><th>Email</th><th>No HP</th><th>Kelas</th></tr></thead>
    <tbody>
    @forelse($siswa as $i => $s)
      <tr>
        <td>{{ $siswa->firstItem() + $i }}</td>
        <td><strong>{{ $s->name }}</strong></td>
        <td>{{ $s->email }}</td>
        <td>{{ $s->nohp ?? '-' }}</td>
        <td>
          @foreach($s->enrollments as $e)
            <span class="badge bg-light text-dark border">{{ $e->kelas->nama }}</span>
          @endforeach
        </td>
      </tr>
    @empty
      <tr><td colspan="5" class="text-center text-muted py-3">Belum ada siswa</td></tr>
    @endforelse
    </tbody>
  </table>
  </div>
  @include('partials._pagination', ['paginator' => $siswa])
</div></div>
@endsection
