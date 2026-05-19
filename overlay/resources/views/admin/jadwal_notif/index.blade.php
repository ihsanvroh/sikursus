@extends('layouts.app')
@section('title','Pengaturan Notifikasi Jadwal')
@section('content')
<div class="card card-stat mb-3"><div class="card-body">
  <h6>Tambah Aturan</h6>
  <small class="text-muted d-block mb-2">Tentukan kapan notif jadwal dikirim ke siswa (H-N atau hari H).</small>
  <form method="POST" action="{{ route('admin.jadwal_notif.store') }}" class="row g-2 align-items-end">@csrf
    <div class="col-md-2">
      <label class="form-label small">Hari Offset</label>
      <input type="number" name="hari_offset" class="form-control" min="-30" max="30" value="1" required>
      <small class="text-muted">1 = H-1, 0 = hari H, 3 = H-3</small>
    </div>
    <div class="col-md-2">
      <label class="form-label small">Jam Kirim</label>
      <input type="time" name="jam_kirim" class="form-control" value="19:00" required>
    </div>
    <div class="col-md-5">
      <label class="form-label small">Keterangan</label>
      <input name="keterangan" class="form-control" placeholder="misal: pengingat malam sebelum kelas">
    </div>
    <div class="col-md-1">
      <div class="form-check">
        <input type="checkbox" name="aktif" id="aktif_new" class="form-check-input" checked value="1">
        <label class="form-check-label" for="aktif_new">Aktif</label>
      </div>
    </div>
    <div class="col-md-2"><button class="btn btn-success w-100"><i class="bi bi-plus-lg"></i> Tambah</button></div>
  </form>
</div></div>

<div class="card card-stat"><div class="card-body">
  <h6>Daftar Aturan</h6>
  <div class="table-responsive">
  <table class="table align-middle text-nowrap">
    <thead><tr><th>Offset</th><th>Jam Kirim</th><th>Keterangan</th><th>Status</th><th class="text-end">Aksi</th></tr></thead>
    <tbody>
    @forelse($settings as $s)
      <tr>
        <td><span class="badge bg-info">{{ $s->labelOffset() }}</span></td>
        <td>{{ substr($s->jam_kirim,0,5) }}</td>
        <td>{{ $s->keterangan ?? '-' }}</td>
        <td>@if($s->aktif)<span class="badge bg-success">Aktif</span>@else<span class="badge bg-secondary">Non-aktif</span>@endif</td>
        <td class="text-end">
          <button class="btn btn-sm btn-outline-warning" data-bs-toggle="modal" data-bs-target="#editSet{{ $s->id }}"><i class="bi bi-pencil"></i></button>
          <form action="{{ route('admin.jadwal_notif.destroy',$s) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus aturan?')">@csrf @method('DELETE')<button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button></form>
        </td>
      </tr>
      <div class="modal fade" id="editSet{{ $s->id }}"><div class="modal-dialog"><div class="modal-content">
        <form method="POST" action="{{ route('admin.jadwal_notif.update',$s) }}">@csrf @method('PUT')
          <div class="modal-header"><h5 class="modal-title">Edit Aturan</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
          <div class="modal-body">
            <div class="mb-2"><label class="form-label">Hari Offset</label><input type="number" name="hari_offset" class="form-control" value="{{ $s->hari_offset }}" min="-30" max="30" required></div>
            <div class="mb-2"><label class="form-label">Jam Kirim</label><input type="time" name="jam_kirim" class="form-control" value="{{ substr($s->jam_kirim,0,5) }}" required></div>
            <div class="mb-2"><label class="form-label">Keterangan</label><input name="keterangan" class="form-control" value="{{ $s->keterangan }}"></div>
            <div class="form-check"><input type="checkbox" name="aktif" id="aktif{{ $s->id }}" class="form-check-input" value="1" @checked($s->aktif)><label for="aktif{{ $s->id }}" class="form-check-label">Aktif</label></div>
          </div>
          <div class="modal-footer"><button class="btn btn-primary">Simpan</button></div>
        </form>
      </div></div></div>
    @empty
      <tr><td colspan="5" class="text-center text-muted py-3">Belum ada aturan</td></tr>
    @endforelse
    </tbody>
  </table>
  </div>
  <div class="alert alert-light small mt-3">
    <i class="bi bi-info-circle"></i> Notif jadwal otomatis dikirim setiap hari berdasarkan aturan aktif.
    Jalankan manual untuk testing: <code>php artisan jadwal:reminder --dry-run</code>
  </div>
</div></div>
@endsection
