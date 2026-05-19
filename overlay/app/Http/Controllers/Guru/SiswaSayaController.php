<?php
namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Models\Kelas;
use App\Models\User;
use App\Http\Controllers\Concerns\PerPage;
use Illuminate\Http\Request;

class SiswaSayaController extends Controller {
    use PerPage;

    /**
     * List siswa yang diajar guru ini (siswa yang aktif enroll di kelas yang dipegang guru).
     */
    public function index(Request $request) {
        $guru = auth()->user();
        // Kelas yang dipegang guru ini
        $kelasIds = Kelas::where('guru_id', $guru->id)->pluck('id');

        $q = User::where('role','siswa')
            ->whereHas('enrollments', fn($e) => $e->whereIn('kelas_id', $kelasIds)->where('status','aktif'))
            ->with(['enrollments' => fn($e) => $e->whereIn('kelas_id', $kelasIds)->where('status','aktif')->with('kelas')]);

        if ($request->filled('q')) {
            $kw = $request->q;
            $q->where(fn($x) => $x->where('name','like',"%$kw%")->orWhere('email','like',"%$kw%"));
        }
        if ($request->filled('kelas_id')) {
            $kid = $request->kelas_id;
            $q->whereHas('enrollments', fn($e) => $e->where('kelas_id',$kid)->where('status','aktif'));
        }

        $siswa = $q->orderBy('name')->paginate($this->perPage(20))->withQueryString();
        $kelasList = Kelas::whereIn('id', $kelasIds)->orderBy('nama')->get();

        return view('guru.siswa.index', compact('siswa','kelasList'));
    }
}
