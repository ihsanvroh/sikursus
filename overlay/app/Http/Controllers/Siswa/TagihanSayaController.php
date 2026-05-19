<?php
namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use App\Models\Tagihan;
use App\Http\Controllers\Concerns\PerPage;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class TagihanSayaController extends Controller {
    use PerPage;

    public function index(Request $request) {
        $q = Tagihan::with(['kelas','pembayaran'])
            ->where('siswa_id', auth()->id());

        if ($request->filled('status')) $q->where('status', $request->status);
        if ($request->filled('periode')) $q->where('periode', $request->periode);
        if ($request->filled('kelas_id')) $q->where('kelas_id', $request->kelas_id);

        $tagihan = $q->orderByDesc('periode')->orderBy('jatuh_tempo')
            ->paginate($this->perPage(15))->withQueryString();

        $periodes = Tagihan::where('siswa_id', auth()->id())
            ->select('periode')->distinct()->orderByDesc('periode')->pluck('periode');

        $kelasList = Tagihan::where('siswa_id', auth()->id())
            ->with('kelas')->get()->pluck('kelas')->unique('id')->values();

        return view('siswa.tagihan.index', compact('tagihan','periodes','kelasList'));
    }

    public function downloadPdf(Tagihan $tagihan) {
        abort_unless($tagihan->siswa_id === auth()->id(), 403);
        $tagihan->load(['siswa','kelas.guru','pembayaran']);
        $pdf = Pdf::loadView('tagihan.pdf', compact('tagihan'))->setPaper('A4', 'portrait');
        $filename = sprintf('tagihan-INV-%06d-%s.pdf', $tagihan->id, $tagihan->periode);
        return $pdf->download($filename);
    }
}
