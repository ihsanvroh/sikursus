@extends('layouts.app')
@section('title','Kirim Pesan Jadwal')
@section('content')
<div class="card card-stat"><div class="card-body">
  <form method="POST" action="{{ route('guru.pesan.send') }}">@csrf
    <div class="row g-3">
      <div class="col-md-6">
        <label class="form-label">Kelas Tujuan *</label>
        <select name="kelas_id" id="kelas_id" class="form-select" required onchange="this.form.action='{{ route('guru.pesan.form') }}';this.form.method='GET';this.form.submit();">
          <option value="">- pilih kelas -</option>
          @foreach($kelasList as $k)
            <option value="{{ $k->id }}" @selected($selectedKelas && $selectedKelas->id == $k->id)>{{ $k->nama }} ({{ $k->kode }})</option>
          @endforeach
        </select>
      </div>

      <div class="col-md-6">
        <label class="form-label">Pakai Template</label>
        <select id="tplPicker" class="form-select">
          <option value="">- pilih template -</option>
          @foreach($templates as $t)
            <option value="{{ $t->id }}"
              data-judul="{{ $t->judul }}"
              data-body="{{ $t->body }}">{{ $t->nama }}</option>
          @endforeach
        </select>
        <small class="text-muted">Variabel akan diisi otomatis saat kirim.</small>
      </div>

      @if($selectedKelas)
        <div class="col-12">
          <label class="form-label">Pilih Siswa <small class="text-muted">(kosongkan untuk kirim ke semua siswa kelas ini)</small></label>
          <div class="border rounded p-2" style="max-height:240px;overflow:auto;">
            @if($siswaList->isEmpty())
              <small class="text-muted">Tidak ada siswa di kelas ini.</small>
            @else
              <div class="form-check">
                <input type="checkbox" class="form-check-input" id="cbAll">
                <label class="form-check-label fw-semibold" for="cbAll">Pilih Semua</label>
              </div>
              <hr class="my-2">
              @foreach($siswaList as $s)
                <div class="form-check">
                  <input type="checkbox" name="siswa_ids[]" value="{{ $s->id }}" class="form-check-input cb-siswa" id="cbS{{ $s->id }}">
                  <label class="form-check-label" for="cbS{{ $s->id }}">{{ $s->name }} <small class="text-muted">({{ $s->email }})</small></label>
                </div>
              @endforeach
            @endif
          </div>
        </div>
      @endif

      <div class="col-12">
        <label class="form-label">Judul Notifikasi *</label>
        <input type="text" id="judulInput" name="judul" class="form-control" required placeholder="Contoh: Besok ada les ya jangan lupa">
      </div>
      <div class="col-12">
        <label class="form-label">Pesan *</label>
        <textarea id="bodyInput" name="body" class="form-control" rows="6" required placeholder="Halo, mengingatkan..."></textarea>
        <div class="form-text">Variabel: <code>{nama_siswa}</code> <code>{kelas}</code> <code>{hari}</code> <code>{jam}</code> <code>{tanggal}</code></div>
      </div>
    </div>

    @if($selectedKelas)
      <div class="mt-3">
        <button class="btn btn-primary"><i class="bi bi-send me-1"></i>Kirim Pesan</button>
        <a href="{{ route('guru.kelas.index') }}" class="btn btn-light">Batal</a>
      </div>
    @endif
  </form>
</div></div>

@push('scripts')
<script>
(function(){
  // Apply template
  const picker = document.getElementById('tplPicker');
  if (picker) {
    picker.addEventListener('change', () => {
      const opt = picker.selectedOptions[0];
      if (!opt || !opt.value) return;
      document.getElementById('judulInput').value = opt.dataset.judul || '';
      document.getElementById('bodyInput').value = opt.dataset.body || '';
    });
  }

  // Pilih semua siswa
  const cbAll = document.getElementById('cbAll');
  if (cbAll) {
    cbAll.addEventListener('change', () => {
      document.querySelectorAll('.cb-siswa').forEach(cb => cb.checked = cbAll.checked);
    });
  }
})();
</script>
@endpush
@endsection
