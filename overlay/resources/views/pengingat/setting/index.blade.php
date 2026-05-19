@extends('layouts.app')
@section('title','Pengaturan Pengingat')
@section('content')
<div class="card card-stat mb-3"><div class="card-body">
  <h6>Tambah Aturan Pengingat Otomatis</h6>
  <form method="POST" action="{{ route('pengingat.setting.store') }}" class="row g-2 align-items-end">@csrf
    <div class="col-md-4"><label class="form-label small">Template</label>
      <select name="template_id" class="form-select" required>
        <option value="">- pilih -</option>
        @foreach($templates as $t)<option value="{{ $t->id }}">{{ $t->nama }}</option>@endforeach
      </select>
    </div>
    <div class="col-md-3"><label class="form-label small">Trigger</label>
      <select name="trigger_type" class="form-select" required>
        <option value="before">Sebelum jatuh tempo</option>
        <option value="on">Tepat di jatuh tempo</option>
        <option value="after">Setelah jatuh tempo</option>
      </select>
    </div>
    <div class="col-md-2"><label class="form-label small">Hari Offset</label><input type="number" name="hari_offset" class="form-control" min="0" max="365" value="3"></div>
    <div class="col-md-1 form-check"><input type="checkbox" name="aktif" id="aktif_new" class="form-check-input" checked value="1"><label class="form-check-label" for="aktif_new">Aktif</label></div>
    <div class="col-md-2"><button class="btn btn-success w-100"><i class="bi bi-plus-lg"></i> Tambah</button></div>
  </form>
</div></div>

<div class="card card-stat"><div class="card-body">
  <h6>Daftar Aturan</h6>
  <table class="table align-middle">
    <thead><tr><th>Trigger</th><th>Template</th><th>Status</th><th class="text-end">Aksi</th></tr></thead>
    <tbody>
    @forelse($settings as $s)
      <tr>
        <td><span class="badge bg-info">{{ $s->labelTrigger() }}</span></td>
        <td>{{ $s->template->nama }}<br><small class="text-muted">{{ \Illuminate\Support\Str::limit($s->template->judul, 60) }}</small></td>
        <td>@if($s->aktif)<span class="badge bg-success">Aktif</span>@else<span class="badge bg-secondary">Non-aktif</span>@endif</td>
        <td class="text-end">
          <button class="btn btn-sm btn-outline-warning" data-bs-toggle="modal" data-bs-target="#editSetting{{ $s->id }}"><i class="bi bi-pencil"></i></button>
          <form action="{{ route('pengingat.setting.destroy',$s) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus aturan?')">@csrf @method('DELETE')<button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button></form>
        </td>
      </tr>
      <div class="modal fade" id="editSetting{{ $s->id }}"><div class="modal-dialog"><div class="modal-content">
        <form method="POST" action="{{ route('pengingat.setting.update',$s) }}">@csrf @method('PUT')
          <div class="modal-header"><h5 class="modal-title">Edit Aturan</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
          <div class="modal-body">
            <div class="mb-2"><label>Template</label><select name="template_id" class="form-select" required>
              @foreach($templates as $t)<option value="{{ $t->id }}" @selected($s->template_id==$t->id)>{{ $t->nama }}</option>@endforeach
            </select></div>
            <div class="mb-2"><label>Trigger</label><select name="trigger_type" class="form-select" required>
              <option value="before" @selected($s->trigger_type=='before')>Sebelum jatuh tempo</option>
              <option value="on" @selected($s->trigger_type=='on')>Tepat di jatuh tempo</option>
              <option value="after" @selected($s->trigger_type=='after')>Setelah jatuh tempo</option>
            </select></div>
            <div class="mb-2"><label>Hari Offset</label><input type="number" name="hari_offset" class="form-control" min="0" max="365" value="{{ $s->hari_offset }}"></div>
            <div class="form-check"><input type="checkbox" name="aktif" id="aktif{{ $s->id }}" class="form-check-input" value="1" @checked($s->aktif)><label class="form-check-label" for="aktif{{ $s->id }}">Aktif</label></div>
          </div>
          <div class="modal-footer"><button class="btn btn-primary">Simpan</button></div>
        </form>
      </div></div></div>
    @empty
      <tr><td colspan="4" class="text-center text-muted py-3">Belum ada aturan</td></tr>
    @endforelse
    </tbody>
  </table>
  <div class="alert alert-light small mt-3">
    <i class="bi bi-info-circle"></i> Pengingat otomatis dikirim setiap hari jam 08:00 berdasarkan aturan aktif. Untuk testing manual jalankan: <code>php artisan pengingat:auto --dry-run</code>
  </div>
</div></div>
@endsection
