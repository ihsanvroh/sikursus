@extends('layouts.app')
@section('title', $siswa->exists ? 'Edit Siswa' : 'Tambah Siswa')
@section('content')
<div class="mb-3">@include('partials._back', ['route' => route('siswa.index')])</div>
<div class="card card-stat" style="max-width:720px;">
  <div class="card-body">
    <form method="POST" action="{{ $siswa->exists ? route('siswa.update',$siswa) : route('siswa.store') }}">
      @csrf
      @if($siswa->exists) @method('PUT') @endif
      <div class="row g-3">
        <div class="col-md-6"><label class="form-label">Nama Lengkap *</label><input name="name" class="form-control" required value="{{ old('name', $siswa->name) }}"></div>
        <div class="col-md-6"><label class="form-label">Email *</label><input type="email" name="email" class="form-control" required value="{{ old('email', $siswa->email) }}"></div>
        <div class="col-md-6"><label class="form-label">Password {{ $siswa->exists ? '(kosongkan jika tidak ganti)' : '*' }}</label><input type="password" name="password" class="form-control" {{ $siswa->exists ? '' : 'required' }}></div>
        <div class="col-md-6"><label class="form-label">No HP</label><input name="nohp" class="form-control" value="{{ old('nohp', $siswa->nohp) }}"></div>
        <div class="col-md-4"><label class="form-label">Gender</label><select name="gender" class="form-select"><option value="">-</option><option value="L" @selected(old('gender',$siswa->gender)=='L')>Laki-laki</option><option value="P" @selected(old('gender',$siswa->gender)=='P')>Perempuan</option></select></div>
        <div class="col-md-4"><label class="form-label">Tanggal Lahir</label><input type="date" name="tgl_lahir" class="form-control" value="{{ old('tgl_lahir', $siswa->tgl_lahir?->format('Y-m-d')) }}"></div>
        <div class="col-md-4 d-flex align-items-end"><div class="form-check"><input type="checkbox" name="aktif" id="aktif" class="form-check-input" value="1" @checked(old('aktif', $siswa->aktif ?? true))><label for="aktif" class="form-check-label">Aktif</label></div></div>
        <div class="col-12"><label class="form-label">Alamat</label><textarea name="alamat" class="form-control" rows="2">{{ old('alamat', $siswa->alamat) }}</textarea></div>
      </div>
      <div class="mt-3">
        <button class="btn btn-primary"><i class="bi bi-check-lg"></i> Simpan</button>
        <a href="{{ route('siswa.index') }}" class="btn btn-light">Batal</a>
      </div>
    </form>
  </div>
</div>
@endsection
