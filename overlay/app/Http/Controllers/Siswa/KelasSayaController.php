<?php
namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use App\Models\Kelas;
use App\Models\Tagihan;
use App\Http\Controllers\Concerns\PerPage;
use Illuminate\Http\Request;

class KelasSayaController extends Controller {
    public function index() {
        $siswa = auth()->user();
        $enrollments = $siswa->enrollments()
            ->with(['kelas.guru','kelas.jadwal'])
            ->latest('tgl_mulai')
            ->get();
        return view('siswa.kelas.index', compact('enrollments'));
    }
}
