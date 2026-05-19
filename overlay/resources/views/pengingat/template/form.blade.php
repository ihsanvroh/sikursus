@extends('layouts.app')
@section('title', $t->exists ? 'Edit Template' : 'Tambah Template')
@section('content')
<div class="mb-3">@include('partials._back', ['route' => route('pengingat.template.index')])</div>
<div class="card card-stat" style="max-width:760px;"><div class="card-body">
  <form method="POST" action="{{ $t->exists ? route('pengingat.template.update',$t) : route('pengingat.template.store') }}">
    @csrf @if($t->exists) @method('PUT') @endif
    <div class="alert alert-info small">
      Variabel: <code>{nama}</code> <code>{kelas}</code> <code>{periode}</code> <code>{nominal}</code> <code>{jatuh_tempo}</code> <code>{sisa}</code>
    </div>
    <div class="mb-3"><label class="form-label">Nama Template *</label><input name="nama" class="form-control" required value="{{ old('nama',$t->nama) }}"></div>
    <div class="mb-3"><label class="form-label">Judul Notifikasi *</label><input name="judul" class="form-control" required value="{{ old('judul',$t->judul) }}"></div>
    <div class="mb-3"><label class="form-label">Body Notifikasi *</label><textarea name="body" class="form-control" rows="6" required>{{ old('body',$t->body) }}</textarea></div>
    <div class="mb-3"><div class="form-check"><input type="checkbox" name="aktif" id="aktif" class="form-check-input" value="1" @checked(old('aktif', $t->aktif ?? true))><label for="aktif" class="form-check-label">Template aktif</label></div></div>
    <button class="btn btn-primary">Simpan</button> <a href="{{ route('pengingat.template.index') }}" class="btn btn-light">Batal</a>
  </form>
</div></div>
@endsection
