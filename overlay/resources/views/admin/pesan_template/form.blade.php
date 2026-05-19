@extends('layouts.app')
@section('title', $t->exists ? 'Edit Template Pesan' : 'Tambah Template Pesan')
@section('content')
<div class="mb-3">@include('partials._back', ['route' => route('admin.pesan_template.index')])</div>
<div class="card card-stat" style="max-width:760px;"><div class="card-body">
  <form method="POST" action="{{ $t->exists ? route('admin.pesan_template.update',$t) : route('admin.pesan_template.store') }}">
    @csrf @if($t->exists) @method('PUT') @endif
    <div class="alert alert-info small">
      Variabel yang bisa dipakai: <code>{nama_siswa}</code> <code>{kelas}</code> <code>{hari}</code> <code>{jam}</code> <code>{tanggal}</code>
    </div>
    <div class="row g-3">
      <div class="col-12"><label class="form-label">Nama Template *</label><input name="nama" class="form-control" required value="{{ old('nama',$t->nama) }}"></div>
      <div class="col-12"><label class="form-label">Judul Notifikasi *</label><input name="judul" class="form-control" required value="{{ old('judul',$t->judul) }}"></div>
      <div class="col-12"><label class="form-label">Body Pesan *</label><textarea name="body" class="form-control" rows="6" required>{{ old('body',$t->body) }}</textarea></div>
      <div class="col-12"><div class="form-check"><input type="checkbox" name="aktif" id="aktif" class="form-check-input" value="1" @checked(old('aktif',$t->aktif ?? true))><label for="aktif" class="form-check-label">Aktif</label></div></div>
    </div>
    <div class="mt-3"><button class="btn btn-primary"><i class="bi bi-check-lg"></i> Simpan</button> <a href="{{ route('admin.pesan_template.index') }}" class="btn btn-light">Batal</a></div>
  </form>
</div></div>
@endsection
