<?php
namespace App\Http\Controllers;

use App\Models\Pembayaran;
use App\Models\Tagihan;
use App\Services\ExcelExporter;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;

class LaporanController extends Controller {
    public function index(Request $request) {
        [$bayar, $perKelas, $perHari, $outstanding, $total, $bulan, $tanggal] = $this->getData($request);
        return view('laporan.index', compact('bayar','total','perKelas','perHari','outstanding','bulan','tanggal'));
    }

    public function exportPdf(Request $request) {
        [$bayar, $perKelas, $perHari, $outstanding, $total, $bulan, $tanggal] = $this->getData($request);
        $pdf = Pdf::loadView('laporan.pdf', compact('bayar','total','perKelas','perHari','outstanding','bulan','tanggal'))
                  ->setPaper('A4', 'portrait');
        $filename = "laporan-{$bulan}" . ($tanggal ? "-{$tanggal}" : '') . ".pdf";
        return $pdf->download($filename);
    }

    public function exportExcel(Request $request, ExcelExporter $excel) {
        [$bayar, $perKelas, $perHari, $outstanding, $total, $bulan, $tanggal] = $this->getData($request);

        $headers = ['Tanggal','Siswa','Kelas','Periode','Nominal','Metode','Admin'];
        $rows = $bayar->map(fn($p) => [
            $p->tanggal_bayar->format('Y-m-d'),
            $p->tagihan->siswa->name ?? '-',
            $p->tagihan->kelas->nama ?? '-',
            $p->tagihan->periode ?? '-',
            (int)$p->nominal,
            ucfirst($p->metode),
            $p->admin->name ?? '-',
        ])->toArray();

        $title = "Laporan Pembayaran $bulan" . ($tanggal ? " (filter: $tanggal)" : '');
        $filename = "laporan-{$bulan}" . ($tanggal ? "-{$tanggal}" : '') . ".xlsx";
        return $excel->download($filename, $headers, $rows, $title);
    }

    private function getData(Request $request): array {
        $bulan = $request->bulan ?? Carbon::now()->format('Y-m');
        $tanggal = $request->tanggal;

        $start = Carbon::parse($bulan.'-01')->startOfMonth();
        $end = $start->copy()->endOfMonth();

        $q = Pembayaran::with(['tagihan.siswa','tagihan.kelas','admin'])
            ->whereBetween('tanggal_bayar', [$start, $end]);
        if ($tanggal) {
            $q->whereDate('tanggal_bayar', $tanggal);
        }
        $bayar = $q->orderBy('tanggal_bayar')->orderBy('id')->get();

        $total = $bayar->sum('nominal');
        $perKelas = $bayar->groupBy(fn($b) => $b->tagihan->kelas->nama ?? '-')
                          ->map(fn($g) => ['count' => $g->count(), 'total' => $g->sum('nominal')]);
        $perHari = $bayar->groupBy(fn($b) => $b->tanggal_bayar->format('Y-m-d'))
                         ->map(fn($g) => ['count' => $g->count(), 'total' => $g->sum('nominal')]);

        $outstanding = Tagihan::with(['siswa','kelas'])->where('periode',$bulan)->where('status','!=','lunas')->get();

        return [$bayar, $perKelas, $perHari, $outstanding, $total, $bulan, $tanggal];
    }
}
