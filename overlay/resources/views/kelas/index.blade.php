@extends('layouts.app')
@section('title','Master Kelas & Tarif')
@section('content')
<div class="card card-stat">
  <div class="card-body">
    <div class="d-flex mb-3">
      <h6 class="mb-0 align-self-center">Daftar Kelas</h6>
      <a href="{{ route('kelas.create') }}" class="btn btn-success ms-auto"><i class="bi bi-plus-lg"></i> Tambah Kelas</a>
    </div>
    <div class="table-responsive">
    <table class="table align-middle text-nowrap">
      <thead><tr><th>Kode</th><th>Nama</th><th>Guru</th><th>Jadwal</th><th>Tarif</th><th>Siswa</th><th>Status</th><th class="text-end">Aksi</th></tr></thead>
      <tbody>
      @forelse($kelas as $k)
        <tr>
          <td><code>{{ $k->kode }}</code></td>
          <td><strong>{{ $k->nama }}</strong>@if($k->deskripsi)<br><small class="text-muted">{{ $k->deskripsi }}</small>@endif</td>
          <td>{{ $k->guru->name ?? '-' }}</td>
          <td>
            @forelse($k->jadwal as $j)
              <span class="badge bg-light text-dark border me-1">{{ $j->label() }}</span>
            @empty
              <small class="text-muted fst-italic">-</small>
            @endforelse
          </td>
          <td>Rp {{ number_format($k->tarif,0,',','.') }}</td>
          <td><span class="badge bg-info">{{ $k->siswa_aktif_count }}</span></td>
          <td>@if($k->aktif)<span class="badge bg-success">Aktif</span>@else<span class="badge bg-secondary">Non-aktif</span>@endif</td>
          <td class="text-end">
            <a href="{{ route('kelas.show',$k) }}" class="btn btn-sm btn-outline-primary"><i class="bi bi-eye"></i></a>
            <a href="{{ route('kelas.edit',$k) }}" class="btn btn-sm btn-outline-warning"><i class="bi bi-pencil"></i></a>
            <form action="{{ route('kelas.destroy',$k) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus kelas?')">@csrf @method('DELETE')<button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button></form>
          </td>
        </tr>
      @empty
        <tr><td colspan="8" class="text-center text-muted py-3">Belum ada kelas</td></tr>
      @endforelse
      </tbody>
    </table>
    </div>
    @include('partials._pagination', ['paginator' => $kelas])
  </div>
</div>
@endsection
