@extends('layouts.app')
@section('title','Buat Tagihan Manual')
@section('content')
<div class="card card-stat" style="max-width:720px;"><div class="card-body">
  <form method="POST" action="{{ route('tagihan.store') }}">@csrf
    <div class="row g-3">
      <div class="col-md-6"><label class="form-label">Siswa *</label>
        <select name="siswa_id" class="form-select" required>
          <option value="">- pilih -</option>
          @foreach($siswa as $s)<option value="{{ $s->id }}" @selected(old('siswa_id')==$s->id)>{{ $s->name }} ({{ $s->email }})</option>@endforeach
        </select>
      </div>
      <div class="col-md-6"><label class="form-label">Kelas *</label>
        <select name="kelas_id" class="form-select" required>
          <option value="">- pilih -</option>
          @foreach($kelas as $k)<option value="{{ $k->id }}" data-tarif="{{ $k->tarif }}" @selected(old('kelas_id')==$k->id)>{{ $k->nama }} ({{ $k->kode }})</option>@endforeach
        </select>
      </div>
      <div class="col-md-4"><label class="form-label">Periode *</label><input type="month" name="periode" class="form-control" required value="{{ old('periode',$tagihan->periode) }}"></div>
      <div class="col-md-4"><label class="form-label">Nominal *</label><input type="number" step="0.01" name="nominal" id="nominal" class="form-control" required value="{{ old('nominal',$tagihan->nominal) }}"></div>
      <div class="col-md-4"><label class="form-label">Jatuh Tempo *</label><input type="date" name="jatuh_tempo" class="form-control" required value="{{ old('jatuh_tempo',$tagihan->jatuh_tempo) }}"></div>
      <div class="col-12"><label class="form-label">Keterangan</label><textarea name="keterangan" class="form-control" rows="2">{{ old('keterangan') }}</textarea></div>
    </div>
    <div class="mt-3"><button class="btn btn-primary">Simpan</button> <a href="{{ route('tagihan.index') }}" class="btn btn-light">Batal</a></div>
  </form>
</div></div>
@push('scripts')
<script>
document.querySelector('[name=kelas_id]').addEventListener('change', function(){
  const tarif = this.options[this.selectedIndex].dataset.tarif;
  if (tarif) document.getElementById('nominal').value = tarif;
});
</script>
@endpush
@endsection
