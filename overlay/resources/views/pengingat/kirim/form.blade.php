@extends('layouts.app')
@section('title','Kirim Pengingat Manual')
@section('content')
<div class="card card-stat" style="max-width:720px;"><div class="card-body">
  <form method="POST" action="{{ route('pengingat.kirim.send') }}">@csrf
    <div class="mb-3"><label class="form-label">Siswa *</label>
      <select name="siswa_id" id="siswa_id" class="form-select" required>
        <option value="">- pilih siswa -</option>
        @foreach($siswa as $s)<option value="{{ $s->id }}">{{ $s->name }} ({{ $s->email }})</option>@endforeach
      </select>
    </div>
    <div class="mb-3"><label class="form-label">Tagihan (opsional)</label>
      <select name="tagihan_id" id="tagihan_id" class="form-select">
        <option value="">- tidak terkait tagihan -</option>
      </select>
      <small class="text-muted">Pilih tagihan supaya placeholder {nominal}, {periode}, dll terisi.</small>
    </div>
    <div class="mb-3"><label class="form-label">Template *</label>
      <select name="template_id" class="form-select" required>
        <option value="">- pilih template -</option>
        @foreach($templates as $t)<option value="{{ $t->id }}">{{ $t->nama }}</option>@endforeach
      </select>
    </div>
    <button class="btn btn-primary"><i class="bi bi-send"></i> Kirim Pengingat</button>
  </form>
</div></div>
@push('scripts')
<script>
document.getElementById('siswa_id').addEventListener('change', async function(){
  const sel = document.getElementById('tagihan_id');
  sel.innerHTML = '<option value="">- tidak terkait tagihan -</option>';
  if (!this.value) return;
  try {
    const r = await fetch('{{ route("pengingat.kirim.ajax_tagihan") }}?siswa_id=' + this.value);
    const data = await r.json();
    data.forEach(t => {
      const o = document.createElement('option');
      o.value = t.id; o.textContent = t.label;
      sel.appendChild(o);
    });
  } catch(e) { console.error(e); }
});
</script>
@endpush
@endsection
