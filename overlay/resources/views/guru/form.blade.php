@extends('layouts.app')
@section('title', $guru->exists ? 'Edit Guru' : 'Tambah Guru')
@section('content')
<div class="mb-3">@include('partials._back', ['route' => route('guru.index')])</div>
<div class="card card-stat" style="max-width:600px;"><div class="card-body">
  <form method="POST" action="{{ $guru->exists ? route('guru.update',$guru) : route('guru.store') }}">
    @csrf @if($guru->exists) @method('PUT') @endif
    <div class="row g-3">
      <div class="col-12"><label class="form-label">Nama *</label><input name="name" class="form-control" required value="{{ old('name',$guru->name) }}"></div>
      <div class="col-md-6"><label class="form-label">Email *</label><input type="email" name="email" class="form-control" required value="{{ old('email',$guru->email) }}"></div>
      <div class="col-md-6"><label class="form-label">No HP</label><input name="nohp" class="form-control" value="{{ old('nohp',$guru->nohp) }}"></div>
      <div class="col-12"><label class="form-label">Password {{ $guru->exists ? '(kosongkan jika tidak ganti)' : '*' }}</label><input type="password" name="password" class="form-control" {{ $guru->exists ? '' : 'required' }}></div>
      <div class="col-12"><div class="form-check"><input type="checkbox" name="aktif" id="aktif" class="form-check-input" value="1" @checked(old('aktif', $guru->aktif ?? true))><label for="aktif" class="form-check-label">Aktif</label></div></div>
    </div>
    <div class="mt-3"><button class="btn btn-primary">Simpan</button> <a href="{{ route('guru.index') }}" class="btn btn-light">Batal</a></div>
  </form>
</div></div>
@endsection
