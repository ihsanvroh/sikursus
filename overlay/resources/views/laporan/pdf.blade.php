<!doctype html>
<html lang="id"><head><meta charset="utf-8"><title>Laporan {{ $bulan }}</title>
<style>
  body { font-family: DejaVu Sans, sans-serif; font-size: 11px; color: #111; }
  h2 { margin: 0 0 4px; font-size: 18px; }
  h3 { margin: 16px 0 6px; font-size: 13px; border-bottom: 1px solid #999; padding-bottom: 2px; }
  table { width: 100%; border-collapse: collapse; margin-bottom: 8px; }
  th, td { border: 1px solid #bbb; padding: 4px 6px; text-align: left; }
  th { background: #e5e7eb; font-weight: bold; }
  td.num, th.num { text-align: right; }
  .muted { color: #666; }
  .summary td { padding: 4px 8px; }
  .summary .label { font-weight: bold; width: 40%; }
  .row { display: flex; gap: 12px; }
  .col { flex: 1; }
</style></head><body>

<h2>Laporan Pembayaran — Si Kursus</h2>
<div class="muted">
  Periode: <strong>{{ \Carbon\Carbon::parse($bulan.'-01')->translatedFormat('F Y') }}</strong>
  @if($tanggal) · Filter harian: <strong>{{ \Carbon\Carbon::parse($tanggal)->translatedFormat('d F Y') }}</strong>@endif
  · Dicetak: {{ now()->translatedFormat('d M Y H:i') }}
</div>

<h3>Ringkasan</h3>
<table class="summary">
  <tr><td class="label">Total Transaksi</td><td>{{ $bayar->count() }}</td></tr>
  <tr><td class="label">Total Pendapatan</td><td>Rp {{ number_format($total,0,',','.') }}</td></tr>
  <tr><td class="label">Tagihan Belum Lunas</td><td>{{ $outstanding->count() }} tagihan</td></tr>
</table>

<table style="width:100%; border:0;">
<tr>
<td style="width:50%; vertical-align:top; border:0; padding-right:6px;">
  <h3 style="margin-top:0;">Per Hari</h3>
  <table>
    <thead><tr><th>Tanggal</th><th class="num">Tx</th><th class="num">Total (Rp)</th></tr></thead>
    <tbody>
      @forelse($perHari as $tgl => $d)
        <tr><td>{{ \Carbon\Carbon::parse($tgl)->format('d/m/Y') }}</td><td class="num">{{ $d['count'] }}</td><td class="num">{{ number_format($d['total'],0,',','.') }}</td></tr>
      @empty
        <tr><td colspan="3" class="muted">Tidak ada</td></tr>
      @endforelse
    </tbody>
  </table>
</td>
<td style="width:50%; vertical-align:top; border:0; padding-left:6px;">
  <h3 style="margin-top:0;">Per Kelas</h3>
  <table>
    <thead><tr><th>Kelas</th><th class="num">Tx</th><th class="num">Total (Rp)</th></tr></thead>
    <tbody>
      @forelse($perKelas as $nama => $d)
        <tr><td>{{ $nama }}</td><td class="num">{{ $d['count'] }}</td><td class="num">{{ number_format($d['total'],0,',','.') }}</td></tr>
      @empty
        <tr><td colspan="3" class="muted">Tidak ada</td></tr>
      @endforelse
    </tbody>
  </table>
</td>
</tr>
</table>

<h3>Detail Pembayaran</h3>
<table>
  <thead><tr>
    <th>Tanggal</th><th>Siswa</th><th>Kelas</th><th>Periode</th>
    <th class="num">Nominal (Rp)</th><th>Metode</th><th>Admin</th>
  </tr></thead>
  <tbody>
    @forelse($bayar as $p)
      <tr>
        <td>{{ $p->tanggal_bayar->format('d/m/Y') }}</td>
        <td>{{ $p->tagihan->siswa->name ?? '-' }}</td>
        <td>{{ $p->tagihan->kelas->nama ?? '-' }}</td>
        <td>{{ $p->tagihan->periode ?? '-' }}</td>
        <td class="num">{{ number_format($p->nominal,0,',','.') }}</td>
        <td>{{ ucfirst($p->metode) }}</td>
        <td>{{ $p->admin->name ?? '-' }}</td>
      </tr>
    @empty
      <tr><td colspan="7" class="muted" style="text-align:center;">Tidak ada data</td></tr>
    @endforelse
  </tbody>
</table>

@if($outstanding->count() > 0)
<h3>Tagihan Belum Lunas Periode {{ $bulan }}</h3>
<table>
  <thead><tr>
    <th>Siswa</th><th>Kelas</th>
    <th class="num">Nominal (Rp)</th><th class="num">Terbayar (Rp)</th><th class="num">Sisa (Rp)</th>
    <th>Jatuh Tempo</th>
  </tr></thead>
  <tbody>
    @foreach($outstanding as $t)
      <tr>
        <td>{{ $t->siswa->name }}</td>
        <td>{{ $t->kelas->nama }}</td>
        <td class="num">{{ number_format($t->nominal,0,',','.') }}</td>
        <td class="num">{{ number_format($t->terbayar,0,',','.') }}</td>
        <td class="num">{{ number_format($t->sisaBayar(),0,',','.') }}</td>
        <td>{{ $t->jatuh_tempo->format('d/m/Y') }}</td>
      </tr>
    @endforeach
  </tbody>
</table>
@endif

<p class="muted" style="margin-top:18px;">— Dokumen ini dihasilkan otomatis dari Si Kursus —</p>
</body></html>
