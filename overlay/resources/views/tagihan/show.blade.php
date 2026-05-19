@extends('layouts.app')
@section('title','Detail Tagihan')
@section('content')
<div class="row g-3">
  <div class="col-md-5">
    <div class="card card-stat"><div class="card-body">
      <h6 class="mb-3">Info Tagihan</h6>
      <table class="table table-sm">
        <tr><th>Siswa</th><td><a href="{{ route('siswa.show',$tagihan->siswa_id) }}">{{ $tagihan->siswa->name }}</a></td></tr>
        <tr><th>Kelas</th><td>{{ $tagihan->kelas->nama }}</td></tr>
        <tr><th>Periode</th><td>{{ $tagihan->periode }}</td></tr>
        <tr><th>Nominal</th><td>Rp {{ number_format($tagihan->nominal,0,',','.') }}</td></tr>
        <tr><th>Terbayar</th><td>Rp {{ number_format($tagihan->terbayar,0,',','.') }}</td></tr>
        <tr><th>Sisa</th><td><strong class="text-danger">Rp {{ number_format($tagihan->sisaBayar(),0,',','.') }}</strong></td></tr>
        <tr><th>Jatuh Tempo</th><td>{{ $tagihan->jatuh_tempo->format('d M Y') }}</td></tr>
        <tr><th>Status</th><td><span class="badge-soft badge-{{ str_replace('_','-',$tagihan->status) }}">{{ str_replace('_',' ',$tagihan->status) }}</span></td></tr>
        @if($tagihan->keterangan)<tr><th>Keterangan</th><td>{{ $tagihan->keterangan }}</td></tr>@endif
      </table>
    </div></div>
  </div>
  <div class="col-md-7">
    @if($tagihan->status !== 'lunas')
    <div class="card card-stat mb-3"><div class="card-body">
      <h6 class="mb-3">Catat Pembayaran</h6>
      <form method="POST" action="{{ route('pembayaran.store',$tagihan) }}">@csrf
        <div class="row g-2">
          <div class="col-md-6"><label class="form-label">Tanggal *</label><input type="date" name="tanggal_bayar" class="form-control" required value="{{ now()->toDateString() }}"></div>
          <div class="col-md-6"><label class="form-label">Nominal *</label><input type="number" step="0.01" name="nominal" class="form-control" required value="{{ $tagihan->sisaBayar() }}" max="{{ $tagihan->sisaBayar() }}"></div>
          <div class="col-md-6"><label class="form-label">Metode</label>
            <select name="metode" class="form-select">
              <option value="tunai">Tunai</option><option value="transfer">Transfer</option><option value="qris">QRIS</option><option value="lainnya">Lainnya</option>
            </select>
          </div>
          <div class="col-md-6"><label class="form-label">Referensi</label><input name="referensi" class="form-control" placeholder="No struk / no transfer"></div>
          <div class="col-12"><label class="form-label">Catatan</label><textarea name="catatan" class="form-control" rows="2"></textarea></div>
        </div>
        <button class="btn btn-primary mt-3"><i class="bi bi-cash-stack"></i> Catat Pembayaran</button>
      </form>
    </div></div>
    @endif

    <div class="card card-stat"><div class="card-body">
      <h6 class="mb-3">Riwayat Pembayaran</h6>
      <table class="table table-sm">
        <thead><tr><th>Tanggal</th><th>Nominal</th><th>Metode</th><th>Referensi</th><th>Admin</th><th></th></tr></thead>
        <tbody>
          @forelse($tagihan->pembayaran as $p)
            <tr>
              <td>{{ $p->tanggal_bayar->format('d M Y') }}</td>
              <td>Rp {{ number_format($p->nominal,0,',','.') }}</td>
              <td>{{ ucfirst($p->metode) }}</td>
              <td>{{ $p->referensi ?? '-' }}</td>
              <td>{{ $p->admin->name ?? '-' }}</td>
              <td><form action="{{ route('pembayaran.destroy',$p) }}" method="POST" onsubmit="return confirm('Hapus pembayaran?')">@csrf @method('DELETE')<button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button></form></td>
            </tr>
          @empty
            <tr><td colspan="6" class="text-center text-muted py-3">Belum ada pembayaran</td></tr>
          @endforelse
        </tbody>
      </table>
    </div></div>
  </div>
</div>
@endsection
