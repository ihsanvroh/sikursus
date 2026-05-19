@extends('layouts.app')
@section('title','Daftar Siswa')
@section('content')
<div class="card card-stat">
  <div class="card-body">
    <div class="d-flex flex-wrap gap-2 mb-3 align-items-center">
      <form method="GET" class="d-flex flex-wrap gap-2 flex-grow-1">
        <input type="text" name="q" value="{{ request('q') }}" class="form-control" placeholder="Cari nama / email / no HP" style="max-width:280px;">
        <select name="kelas_id" class="form-select" style="max-width:240px;">
          <option value="">Semua kelas</option>
          @foreach($kelas as $k)
            <option value="{{ $k->id }}" @selected(request('kelas_id')==$k->id)>{{ $k->nama }}</option>
          @endforeach
        </select>
        <button class="btn btn-primary"><i class="bi bi-funnel"></i> Filter</button>
        @if(request('q') || request('kelas_id'))
          <a href="{{ route('siswa.index') }}" class="btn btn-outline-secondary">Reset</a>
        @endif
      </form>
      <a href="{{ route('siswa.create') }}" class="btn btn-success ms-auto"><i class="bi bi-plus-lg"></i> Tambah Siswa</a>
    </div>

    <div class="table-responsive">
      <table class="table align-middle">
        <thead><tr><th>#</th><th>Nama</th><th>Email</th><th>No HP</th><th>Kelas Aktif</th><th>Status</th><th class="text-end">Aksi</th></tr></thead>
        <tbody>
        @forelse($siswa as $i => $s)
          <tr>
            <td>{{ $siswa->firstItem() + $i }}</td>
            <td><strong>{{ $s->name }}</strong></td>
            <td>{{ $s->email }}</td>
            <td>{{ $s->nohp ?? '-' }}</td>
            <td><span class="badge bg-info">{{ $s->enrollments_aktif }}</span></td>
            <td>@if($s->aktif)<span class="badge bg-success">Aktif</span>@else<span class="badge bg-secondary">Non-aktif</span>@endif</td>
            <td class="text-end">
              <a href="{{ route('siswa.show',$s) }}" class="btn btn-sm btn-outline-primary"><i class="bi bi-eye"></i></a>
              <a href="{{ route('siswa.edit',$s) }}" class="btn btn-sm btn-outline-warning"><i class="bi bi-pencil"></i></a>
              <form action="{{ route('siswa.destroy',$s) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus siswa?')">@csrf @method('DELETE')<button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button></form>
            </td>
          </tr>
        @empty
          <tr><td colspan="7" class="text-center text-muted py-3">Tidak ada siswa</td></tr>
        @endforelse
        </tbody>
      </table>
    </div>
    {{-- pagination --}}
    @include('partials._pagination', ['paginator' => $siswa])
  </div>
</div>
@endsection
