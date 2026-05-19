@extends('layouts.app')
@section('title', $kelas->exists ? 'Edit Kelas' : 'Tambah Kelas')
@section('content')
<div class="mb-3">@include('partials._back', ['route' => route('kelas.index')])</div>
<div class="card card-stat" style="max-width:860px;">
  <div class="card-body">
    <form method="POST" action="{{ $kelas->exists ? route('kelas.update',$kelas) : route('kelas.store') }}" id="formKelas">
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
          <small class="text-muted">Satu guru bisa pegang banyak kelas, tapi jadwal tidak boleh bentrok.</small>
        </div>
        <div class="col-12"><label class="form-label">Deskripsi</label><textarea name="deskripsi" class="form-control" rows="2">{{ old('deskripsi',$kelas->deskripsi) }}</textarea></div>
        <div class="col-12"><div class="form-check"><input type="checkbox" name="aktif" id="aktif" class="form-check-input" value="1" @checked(old('aktif', $kelas->aktif ?? true))><label for="aktif" class="form-check-label">Kelas aktif</label></div></div>
      </div>

      <hr class="my-4">

      <div class="d-flex align-items-center mb-2">
        <h6 class="mb-0">Jadwal Kelas</h6>
        <small class="text-muted ms-2">slot mingguan rutin</small>
        <button type="button" class="btn btn-sm btn-outline-primary ms-auto" id="btnAddJadwal">
          <i class="bi bi-plus-lg"></i> Tambah Slot
        </button>
      </div>

      <div id="jadwalRows">
        @php
          $oldJadwal = old('jadwal');
          $existing = $kelas->exists && $kelas->relationLoaded('jadwal') ? $kelas->jadwal->toArray() : [];
          // priority: old input → existing data → empty
          if (is_array($oldJadwal) && count($oldJadwal) > 0) {
            $rows = $oldJadwal;
          } elseif (count($existing) > 0) {
            $rows = array_map(fn($j) => [
              'hari' => $j['hari'],
              'jam_mulai' => substr($j['jam_mulai'], 0, 5),
              'jam_selesai' => substr($j['jam_selesai'], 0, 5),
            ], $existing);
          } else {
            $rows = [];
          }
        @endphp

        @forelse($rows as $i => $r)
          <div class="row g-2 align-items-end mb-2 jadwal-row">
            <div class="col-4 col-md-3">
              <label class="form-label small">Hari</label>
              <select name="jadwal[{{ $i }}][hari]" class="form-select form-select-sm">
                @foreach(\App\Models\JadwalKelas::HARI as $idx => $nama)
                  <option value="{{ $idx }}" @selected(($r['hari'] ?? null) == $idx)>{{ $nama }}</option>
                @endforeach
              </select>
            </div>
            <div class="col-4 col-md-3">
              <label class="form-label small">Mulai</label>
              <input type="time" name="jadwal[{{ $i }}][jam_mulai]" class="form-control form-control-sm" value="{{ $r['jam_mulai'] ?? '08:00' }}">
            </div>
            <div class="col-4 col-md-3">
              <label class="form-label small">Selesai</label>
              <input type="time" name="jadwal[{{ $i }}][jam_selesai]" class="form-control form-control-sm" value="{{ $r['jam_selesai'] ?? '09:00' }}">
            </div>
            <div class="col-12 col-md-3">
              <button type="button" class="btn btn-outline-danger btn-sm w-100 btn-remove-jadwal">
                <i class="bi bi-trash"></i> Hapus
              </button>
            </div>
          </div>
        @empty
          <div class="text-muted small fst-italic" id="emptyHint">Belum ada jadwal. Klik "Tambah Slot" untuk menambah.</div>
        @endforelse
      </div>

      <div class="mt-4">
        <button class="btn btn-primary"><i class="bi bi-check-lg"></i> Simpan</button>
        <a href="{{ route('kelas.index') }}" class="btn btn-light">Batal</a>
      </div>
    </form>
  </div>
</div>

{{-- Template baris jadwal (hidden, di-clone via JS) --}}
<template id="tplJadwalRow">
  <div class="row g-2 align-items-end mb-2 jadwal-row">
    <div class="col-4 col-md-3">
      <label class="form-label small">Hari</label>
      <select name="jadwal[__INDEX__][hari]" class="form-select form-select-sm">
        @foreach(\App\Models\JadwalKelas::HARI as $idx => $nama)
          <option value="{{ $idx }}">{{ $nama }}</option>
        @endforeach
      </select>
    </div>
    <div class="col-4 col-md-3">
      <label class="form-label small">Mulai</label>
      <input type="time" name="jadwal[__INDEX__][jam_mulai]" class="form-control form-control-sm" value="08:00">
    </div>
    <div class="col-4 col-md-3">
      <label class="form-label small">Selesai</label>
      <input type="time" name="jadwal[__INDEX__][jam_selesai]" class="form-control form-control-sm" value="09:00">
    </div>
    <div class="col-12 col-md-3">
      <button type="button" class="btn btn-outline-danger btn-sm w-100 btn-remove-jadwal">
        <i class="bi bi-trash"></i> Hapus
      </button>
    </div>
  </div>
</template>

@push('scripts')
<script>
(function(){
  const wrap = document.getElementById('jadwalRows');
  const tpl = document.getElementById('tplJadwalRow').innerHTML;
  const btnAdd = document.getElementById('btnAddJadwal');
  let counter = wrap.querySelectorAll('.jadwal-row').length;

  btnAdd.addEventListener('click', () => {
    const hint = document.getElementById('emptyHint');
    if (hint) hint.remove();
    const html = tpl.replaceAll('__INDEX__', counter++);
    const div = document.createElement('div');
    div.innerHTML = html;
    wrap.appendChild(div.firstElementChild);
  });

  wrap.addEventListener('click', (e) => {
    const btn = e.target.closest('.btn-remove-jadwal');
    if (!btn) return;
    btn.closest('.jadwal-row').remove();
  });
})();
</script>
@endpush
@endsection
