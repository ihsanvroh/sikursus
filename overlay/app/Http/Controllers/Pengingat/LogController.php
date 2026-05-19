<?php
namespace App\Http\Controllers\Pengingat;

use App\Http\Controllers\Controller;
use App\Models\PengingatLog;
use Illuminate\Http\Request;
use App\Http\Controllers\Concerns\PerPage;

class LogController extends Controller {
    use PerPage;

    public function index(Request $r) {
        $q = PengingatLog::with(['siswa','tagihan.kelas','template']);
        if ($r->filled('mode')) $q->where('mode', $r->mode);
        if ($r->filled('status')) $q->where('status', $r->status);
        if ($r->filled('q')) {
            $kw = $r->q;
            $q->whereHas('siswa', fn($s) => $s->where('name','like',"%$kw%"));
        }
        $logs = $q->orderByDesc('sent_at')->paginate($this->perPage(30))->withQueryString();
        return view('pengingat.log.index', compact('logs'));
    }
}
