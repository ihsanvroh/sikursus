<!doctype html>
<html lang="id"><head><meta charset="utf-8"><title>Daftar Tagihan</title>
<style>
  body { font-family: DejaVu Sans, sans-serif; font-size: 10px; color: #111; }
  h2 { margin: 0 0 4px; font-size: 16px; }
  .muted { color: #666; font-size: 10px; }
  table { width: 100%; border-collapse: collapse; margin-top: 8px; }
  th, td { border: 1px solid #bbb; padding: 4px 6px; text-align: left; }
  th { background: #e5e7eb; }
  td.num, th.num { text-align: right; }
  .badge { padding: 2px 6px; border-radius: 3px; font-size: 9px; }
  .badge-belum-bayar { background: #fee2e2; color: #991b1b; }
  .badge-sebagian { background: #fef3c7; color: #92400e; }
  .badge-lunas { background: #dcfce7; color: #166534; }
</style></head><body>

<h2>Daftar Tagihan — Si Kursus</h2>
<div class="muted">
  Dicetak: {{ now()->translatedFormat('d F Y H:i') }} · Total: {{ $tagihan->count() }} tagihan
  @if(!empty($filters))
    <br>Filter: {{ implode(' · ', $filters) }}
  @endif
</div>

<table>
  <thead><tr>
    <th>Periode</th><th>Siswa</th><th>Kelas</th>
    <th class="num">Nominal</th><th class="num">Terbayar</th><th class="num">Sisa</th>
    <th>Jatuh Tempo</th><th>Status</th>
  </tr></thead>
  <tbody>
    @forelse($tagihan as $t)
      <tr>
        <td>{{ $t->periode }}</td>
        <td>{{ $t->siswa->name ?? '-' }}</td>
        <td>{{ $t->kelas->nama ?? '-' }}</td>
        <td class="num">{{ number_format($t->nominal,0,',','.') }}</td>
        <td class="num">{{ number_format($t->terbayar,0,',','.') }}</td>
        <td class="num">{{ number_format($t->sisaBayar(),0,',','.') }}</td>
        <td>{{ $t->jatuh_tempo->format('d/m/Y') }}</td>
        <td><span class="badge badge-{{ str_replace('_','-',$t->status) }}">{{ str_replace('_',' ',strtoupper($t->status)) }}</span></td>
      </tr>
    @empty
      <tr><td colspan="8" class="muted" style="text-align:center;">Tidak ada data</td></tr>
    @endforelse
  </tbody>
</table>

</body></html>
