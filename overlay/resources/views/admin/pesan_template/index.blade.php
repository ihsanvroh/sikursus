@extends('layouts.app')
@section('title','Template Pesan Jadwal')
@section('content')
<div class="card card-stat"><div class="card-body">
  <div class="d-flex flex-wrap gap-2 mb-3 align-items-center">
    <h6 class="mb-0">Template Pesan Jadwal Kelas</h6>
    <small class="text-muted ms-2">Dipakai guru saat kirim notif jadwal</small>
    <a href="{{ route('admin.pesan_template.create') }}" class="btn btn-success ms-auto"><i class="bi bi-plus-lg"></i> Tambah</a>
  </div>

  <div class="table-responsive">
  <table class="table align-middle text-nowrap">
    <thead><tr><th>Nama</th><th>Judul</th><th>Body (preview)</th><th>Status</th><th class="text-end">Aksi</th></tr></thead>
    <tbody>
    @forelse($template as $t)
      <tr>
        <td><strong>{{ $t->nama }}</strong></td>
        <td>{{ $t->judul }}</td>
        <td><small class="text-muted">{{ \Illuminate\Support\Str::limit($t->body, 80) }}</small></td>
        <td>@if($t->aktif)<span class="badge bg-success">Aktif</span>@else<span class="badge bg-secondary">Non-aktif</span>@endif</td>
        <td class="text-end">
          <a href="{{ route('admin.pesan_template.edit',$t) }}" class="btn btn-sm btn-outline-warning"><i class="bi bi-pencil"></i></a>
          <form action="{{ route('admin.pesan_template.destroy',$t) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus template?')">@csrf @method('DELETE')<button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button></form>
        </td>
      </tr>
    @empty
      <tr><td colspan="5" class="text-center text-muted py-3">Belum ada template</td></tr>
    @endforelse
    </tbody>
  </table>
  </div>
  @include('partials._pagination', ['paginator' => $template])
</div></div>
@endsection
