@extends('layouts.app')
@section('title','Detail Siswa: '.$siswa->name)
@section('content')
<div class="row g-3">
  <div class="col-md-4">
    <div class="card card-stat">
      <div class="card-body">
        <div class="text-center mb-3">
          <div class="display-1 text-muted"><i class="bi bi-person-circle"></i></div>
          <h5 class="mb-0">{{ $siswa->name }}</h5>
          <small class="text-muted">{{ $siswa->email }}</small>
        </div>
        <table class="table table-sm">
          <tr><th>No HP</th><td>{{ $siswa->nohp ?? '-' }}</td></tr>
          <tr><th>Gender</th><td>{{ $siswa->gender == 'L' ? 'Laki-laki' : ($siswa->gender == 'P' ? 'Perempuan' : '-') }}</td></tr>
          <tr><th>Tgl Lahir</th><td>{{ $siswa->tgl_lahir?->format('d M Y') ?? '-' }}</td></tr>
          <tr><th>Alamat</th><td>{{ $siswa->alamat ?? '-' }}</td></tr>
          <tr><th>Status</th><td>@if($siswa->aktif)<span class="badge bg-success">Aktif</span>@else<span class="badge bg-secondary">Non-aktif</span>@endif</td></tr>
        </table>
        <a href="{{ route('siswa.edit',$siswa) }}" class="btn btn-warning w-100"><i class="bi bi-pencil"></i> Edit Profil</a>
      </div>
    </div>
  </div>

  <div class="col-md-8">
    <div class="card card-stat mb-3">
      <div class="card-body">
        <div class="d-flex align-items-center mb-3">
          <h6 class="mb-0">Riwayat Kelas</h6>
          <button class="btn btn-success btn-sm ms-auto" data-bs-toggle="modal" data-bs-target="#tambahKelasModal"><i class="bi bi-plus-lg"></i> Tambah Kelas</button>
        </div>
        <table class="table table-sm">
          <thead><tr><th>Kelas</th><th>Guru</th><th>Mulai</th><th>Selesai</th><th>Status</th><th class="text-end">Aksi</th></tr></thead>
          <tbody>
            @forelse($enrollments as $e)
              <tr>
                <td><strong>{{ $e->kelas->nama }}</strong><br><small class="text-muted">{{ $e->kelas->kode }} · Rp {{ number_format($e->kelas->tarif,0,',','.') }}</small></td>
                <td>{{ $e->kelas->guru->name ?? '-' }}</td>
                <td>{{ $e->tgl_mulai?->format('d M Y') }}</td>
                <td>{{ $e->tgl_selesai?->format('d M Y') ?? '-' }}</td>
                <td><span class="badge-soft badge-{{ $e->status }}">{{ $e->status }}</span></td>
                <td class="text-end">
                  @if($e->status == 'aktif')
                    <button class="btn btn-sm btn-outline-info" data-bs-toggle="modal" data-bs-target="#pindahModal{{ $e->id }}"><i class="bi bi-arrow-left-right"></i> Pindah</button>
                    <form action="{{ route('siswa.enrollment_selesai',$e) }}" method="POST" class="d-inline" onsubmit="return confirm('Tandai selesai?')">@csrf<button class="btn btn-sm btn-outline-secondary"><i class="bi bi-check"></i> Selesai</button></form>
                  @endif
                </td>
              </tr>
              @if($e->status == 'aktif')
              <div class="modal fade" id="pindahModal{{ $e->id }}"><div class="modal-dialog"><div class="modal-content">
                <form method="POST" action="{{ route('siswa.ganti_kelas',$e) }}">@csrf
                  <div class="modal-header"><h5 class="modal-title">Pindah dari: {{ $e->kelas->nama }}</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                  <div class="modal-body">
                    <div class="mb-2"><label>Kelas baru</label>
                      <select name="kelas_baru_id" class="form-select" required>
                        @foreach($kelasTersedia as $k)
                          <option value="{{ $k->id }}">{{ $k->nama }} ({{ $k->kode }}) - Rp {{ number_format($k->tarif,0,',','.') }}</option>
                        @endforeach
                      </select>
                    </div>
                    <div class="mb-2"><label>Tanggal pindah</label><input type="date" name="tgl_pindah" class="form-control" value="{{ now()->toDateString() }}"></div>
                    <div class="mb-2"><label>Catatan</label><textarea name="catatan" class="form-control" rows="2"></textarea></div>
                  </div>
                  <div class="modal-footer"><button class="btn btn-primary">Pindahkan</button></div>
                </form>
              </div></div></div>
              @endif
            @empty
              <tr><td colspan="6" class="text-center text-muted py-3">Belum terdaftar di kelas manapun</td></tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>

    <div class="card card-stat">
      <div class="card-body">
        <h6 class="mb-3">Riwayat Tagihan & Pembayaran</h6>
        <table class="table table-sm">
          <thead><tr><th>Periode</th><th>Kelas</th><th>Nominal</th><th>Terbayar</th><th>Status</th><th>Pembayaran</th></tr></thead>
          <tbody>
            @forelse($tagihan as $t)
              <tr>
                <td>{{ $t->periode }}</td>
                <td>{{ $t->kelas->nama }}</td>
                <td>Rp {{ number_format($t->nominal,0,',','.') }}</td>
                <td>Rp {{ number_format($t->terbayar,0,',','.') }}</td>
                <td><span class="badge-soft badge-{{ str_replace('_','-',$t->status) }}">{{ str_replace('_',' ',$t->status) }}</span></td>
                <td>
                  @forelse($t->pembayaran as $p)
                    <div class="small">{{ $p->tanggal_bayar->format('d/m/Y') }}: Rp {{ number_format($p->nominal,0,',','.') }} <span class="text-muted">({{ $p->metode }})</span></div>
                  @empty
                    <span class="text-muted small">-</span>
                  @endforelse
                </td>
              </tr>
            @empty
              <tr><td colspan="6" class="text-center text-muted py-3">Belum ada tagihan</td></tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>

<!-- Tambah Kelas Modal -->
<div class="modal fade" id="tambahKelasModal"><div class="modal-dialog"><div class="modal-content">
  <form method="POST" action="{{ route('siswa.tambah_kelas',$siswa) }}">@csrf
    <div class="modal-header"><h5 class="modal-title">Daftarkan {{ $siswa->name }} ke Kelas</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
    <div class="modal-body">
      @if($kelasTersedia->isEmpty())
        <div class="alert alert-warning">Tidak ada kelas tersedia (siswa sudah terdaftar di semua kelas aktif).</div>
      @else
        <div class="mb-2"><label>Kelas</label>
          <select name="kelas_id" class="form-select" required>
            @foreach($kelasTersedia as $k)
              <option value="{{ $k->id }}">{{ $k->nama }} ({{ $k->kode }}) - Rp {{ number_format($k->tarif,0,',','.') }}</option>
            @endforeach
          </select>
        </div>
        <div class="mb-2"><label>Tanggal mulai</label><input type="date" name="tgl_mulai" class="form-control" value="{{ now()->toDateString() }}"></div>
        <div class="mb-2"><label>Catatan</label><textarea name="catatan" class="form-control" rows="2"></textarea></div>
      @endif
    </div>
    <div class="modal-footer"><button class="btn btn-primary" {{ $kelasTersedia->isEmpty() ? 'disabled' : '' }}>Daftarkan</button></div>
  </form>
</div></div></div>
@endsection
