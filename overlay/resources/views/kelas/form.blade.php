@extends('layouts.app')
@section('title', $kelas->exists ? 'Edit Kelas' : 'Tambah Kelas')
@section('content')
<div class="card card-stat" style="max-width:720px;">
  <div class="card-body">
    <form method="POST" action="{{ $kelas->exists ? route('kelas.update',$kelas) : route('kelas.store') }}">
      @csrf @if($kelas->exists) @method('PUT') @endif
      <div class="row g-3">
        <div class="col-md-8"><label class="form-label">Nama Kelas *</label><input name="nama" class="form-control" required value="{{ old('nama',$kelas->nama) }}"></div>
        <div class="col-md-4"><label class="form-label">Kode *</label><input name="kode" class="form-control" required value="{{ old('kode',$kelas->kode) }}"></div>
        <div class="col-md-6"><label class="form-label">Tarif (Rp) *</label><input type="number" step="0.01" name="tarif" class="form-control" required value="{{ old('tarif',$kelas->tarif) }}"></div>
        <div class="col-md-6"><label class="form-label">Guru Penanggung Jawab</label>
          <select name="guru_id" class="form-select">
            <option value="">-</option>
            @foreach($guru as $g)<option value="{{ $g->id }}" @selected(old('guru_id',$kelas->guru_id)==$g->id)>{{ $g->name }}</option>@endforeach
          </select>
        </div>
        <div class="col-12"><label class="form-label">Deskripsi</label><textarea name="deskripsi" class="form-control" rows="3">{{ old('deskripsi',$kelas->deskripsi) }}</textarea></div>
        <div class="col-12"><div class="form-check"><input type="checkbox" name="aktif" id="aktif" class="form-check-input" value="1" @checked(old('aktif', $kelas->aktif ?? true))><label for="aktif" class="form-check-label">Kelas aktif</label></div></div>
      </div>
      <div class="mt-3"><button class="btn btn-primary">Simpan</button> <a href="{{ route('kelas.index') }}" class="btn btn-light">Batal</a></div>
    </form>
  </div>
</div>
@endsection
