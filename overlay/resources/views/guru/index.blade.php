@extends('layouts.app')
@section('title','Daftar Guru')
@section('content')
<div class="card card-stat"><div class="card-body">
  <div class="d-flex mb-3"><h6 class="mb-0 align-self-center">Daftar Guru</h6><a href="{{ route('guru.create') }}" class="btn btn-success ms-auto"><i class="bi bi-plus-lg"></i> Tambah Guru</a></div>
  <table class="table align-middle">
    <thead><tr><th>Nama</th><th>Email</th><th>No HP</th><th>Kelas Diampu</th><th>Status</th><th class="text-end">Aksi</th></tr></thead>
    <tbody>
    @forelse($guru as $g)
      <tr>
        <td><strong>{{ $g->name }}</strong></td>
        <td>{{ $g->email }}</td>
        <td>{{ $g->nohp ?? '-' }}</td>
        <td><span class="badge bg-info">{{ $g->kelas_diampu_count }}</span></td>
        <td>@if($g->aktif)<span class="badge bg-success">Aktif</span>@else<span class="badge bg-secondary">Non-aktif</span>@endif</td>
        <td class="text-end">
          <a href="{{ route('guru.edit',$g) }}" class="btn btn-sm btn-outline-warning"><i class="bi bi-pencil"></i></a>
          <form action="{{ route('guru.destroy',$g) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus guru?')">@csrf @method('DELETE')<button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button></form>
        </td>
      </tr>
    @empty
      <tr><td colspan="6" class="text-center text-muted py-3">Belum ada guru</td></tr>
    @endforelse
    </tbody>
  </table>
  @include('partials._pagination', ['paginator' => $guru])
</div></div>
@endsection
