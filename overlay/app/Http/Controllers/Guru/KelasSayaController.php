<?php
namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Models\Kelas;

class KelasSayaController extends Controller {
    public function index() {
        $guru = auth()->user();
        $kelas = Kelas::where('guru_id', $guru->id)
            ->with(['jadwal','siswaAktif'])
            ->withCount(['siswaAktif as siswa_aktif_count'])
            ->orderBy('nama')
            ->get();
        return view('guru.kelas.index', compact('kelas'));
    }

    public function show(Kelas $kela) {
        abort_unless($kela->guru_id === auth()->id(), 403);
        $kelas = $kela->load(['jadwal', 'siswa' => fn($q) => $q->wherePivot('status','aktif')]);
        return view('guru.kelas.show', compact('kelas'));
    }
}
