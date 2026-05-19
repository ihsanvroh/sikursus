<!doctype html>
<html lang="id"><head><meta charset="utf-8"><title>Tagihan #{{ $tagihan->id }}</title>
<style>
  body { font-family: DejaVu Sans, sans-serif; font-size: 12px; color: #111; padding: 10px; }
  .header { border-bottom: 2px solid #4338ca; padding-bottom: 8px; margin-bottom: 12px; }
  .header h1 { margin: 0; font-size: 22px; color: #4338ca; }
  .header .sub { color: #555; }
  .info-row { display: flex; gap: 20px; margin-bottom: 12px; }
  .info-block { flex: 1; }
  .info-block h4 { margin: 0 0 4px; font-size: 11px; color: #666; text-transform: uppercase; letter-spacing: 0.5px; }
  table { width: 100%; border-collapse: collapse; margin-bottom: 12px; }
  th, td { padding: 8px 10px; text-align: left; }
  th { background: #f3f4f6; font-weight: bold; border-bottom: 1px solid #ccc; }
  td { border-bottom: 1px solid #eee; }
  td.num { text-align: right; }
  .total-row td { font-weight: bold; background: #fef3c7; border-top: 2px solid #92400e; }
  .badge { padding: 4px 10px; border-radius: 4px; font-size: 11px; font-weight: bold; display: inline-block; }
  .badge-belum { background: #fee2e2; color: #991b1b; }
  .badge-sebagian { background: #fef3c7; color: #92400e; }
  .badge-lunas { background: #dcfce7; color: #166534; }
  .footer { margin-top: 30px; font-size: 10px; color: #999; text-align: center; border-top: 1px solid #eee; padding-top: 8px; }
</style></head><body>

<div class="header">
  <h1>TAGIHAN KURSUS</h1>
  <div class="sub">Si Kursus · No. INV-{{ str_pad($tagihan->id, 6, '0', STR_PAD_LEFT) }}</div>
</div>

<div class="info-row">
  <div class="info-block">
    <h4>Ditagihkan kepada</h4>
    <div><strong>{{ $tagihan->siswa->name }}</strong></div>
    <div>{{ $tagihan->siswa->email }}</div>
    @if($tagihan->siswa->nohp)<div>{{ $tagihan->siswa->nohp }}</div>@endif
  </div>
  <div class="info-block" style="text-align:right;">
    <h4>Detail Tagihan</h4>
    <div>Periode: <strong>{{ $tagihan->periode }}</strong></div>
    <div>Jatuh Tempo: <strong>{{ $tagihan->jatuh_tempo->format('d M Y') }}</strong></div>
    <div>Status:
      @php $st = str_replace('_','-',$tagihan->status); @endphp
      <span class="badge badge-{{ $st }}">{{ str_replace('_',' ',strtoupper($tagihan->status)) }}</span>
    </div>
  </div>
</div>

<table>
  <thead><tr><th>Deskripsi</th><th class="num">Nominal</th></tr></thead>
  <tbody>
    <tr>
      <td>
        <strong>{{ $tagihan->kelas->nama }}</strong> ({{ $tagihan->kelas->kode }})
        @if($tagihan->kelas->guru)<br><small>Guru: {{ $tagihan->kelas->guru->name }}</small>@endif
        @if($tagihan->keterangan)<br><small style="color:#666;">{{ $tagihan->keterangan }}</small>@endif
      </td>
      <td class="num">Rp {{ number_format($tagihan->nominal,0,',','.') }}</td>
    </tr>
    <tr>
      <td class="num">Sudah Dibayar</td>
      <td class="num">- Rp {{ number_format($tagihan->terbayar,0,',','.') }}</td>
    </tr>
    <tr class="total-row">
      <td class="num">SISA YANG HARUS DIBAYAR</td>
      <td class="num">Rp {{ number_format($tagihan->sisaBayar(),0,',','.') }}</td>
    </tr>
  </tbody>
</table>

@if($tagihan->pembayaran && $tagihan->pembayaran->count() > 0)
<h4 style="margin-top:16px;">Riwayat Pembayaran</h4>
<table>
  <thead><tr><th>Tanggal</th><th>Metode</th><th>Catatan</th><th class="num">Nominal</th></tr></thead>
  <tbody>
    @foreach($tagihan->pembayaran as $p)
      <tr>
        <td>{{ $p->tanggal_bayar->format('d M Y') }}</td>
        <td>{{ ucfirst($p->metode) }}</td>
        <td>{{ $p->catatan ?? '-' }}</td>
        <td class="num">Rp {{ number_format($p->nominal,0,',','.') }}</td>
      </tr>
    @endforeach
  </tbody>
</table>
@endif

<div class="footer">
  Dicetak pada {{ now()->translatedFormat('d F Y H:i') }} · Si Kursus
</div>

</body></html>
