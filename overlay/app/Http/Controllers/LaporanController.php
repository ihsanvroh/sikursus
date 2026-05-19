<?php
namespace App\Http\Controllers;

use App\Models\Pembayaran;
use App\Models\Tagihan;
use Illuminate\Http\Request;
use Carbon\Carbon;

class LaporanController extends Controller {
    public function index(Request $request) {
        $bulan = $request->bulan ?? Carbon::now()->format('Y-m');
        $tanggal = $request->tanggal; // optional filter harian YYYY-MM-DD

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

        // Outstanding tagihan (belum lunas) untuk bulan ini
        $outstanding = Tagihan::with(['siswa','kelas'])->where('periode',$bulan)->where('status','!=','lunas')->get();

        return view('laporan.index', compact('bayar','total','perKelas','perHari','outstanding','bulan','tanggal'));
    }
}
