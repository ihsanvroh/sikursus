<?php
namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Models\Kelas;
use App\Models\PesanJadwalTemplate;
use App\Models\User;
use App\Notifications\PesanJadwalNotification;
use Illuminate\Http\Request;

class PesanJadwalController extends Controller {
    public function form(Request $r) {
        $guru = auth()->user();
        $kelasList = Kelas::where('guru_id', $guru->id)->orderBy('nama')->get();
        $templates = PesanJadwalTemplate::where('aktif', true)->orderBy('nama')->get();
        $selectedKelas = $r->kelas_id ? Kelas::find($r->kelas_id) : null;
        $siswaList = collect();
        if ($selectedKelas && $selectedKelas->guru_id === $guru->id) {
            $siswaList = $selectedKelas->siswa()->wherePivot('status','aktif')->get();
        }
        return view('guru.pesan.form', compact('kelasList','templates','selectedKelas','siswaList'));
    }

    public function send(Request $r) {
        $guru = auth()->user();
        $data = $r->validate([
            'kelas_id' => 'required|exists:kelas,id',
            'judul' => 'required|string|max:200',
            'body' => 'required|string',
            'siswa_ids' => 'nullable|array',
            'siswa_ids.*' => 'exists:users,id',
        ]);

        $kelas = Kelas::findOrFail($data['kelas_id']);
        if ($kelas->guru_id !== $guru->id) {
            return back()->with('error','Anda bukan guru kelas ini.');
        }

        // Target: kalau kosong = semua siswa aktif di kelas ini
        if (empty($data['siswa_ids'])) {
            $siswaIds = $kelas->siswa()->wherePivot('status','aktif')->pluck('users.id')->toArray();
        } else {
            $siswaIds = $data['siswa_ids'];
        }

        $sent = 0;
        $siswaList = User::whereIn('id', $siswaIds)->where('role','siswa')->get();
        foreach ($siswaList as $s) {
            $s->notify(new PesanJadwalNotification(
                judul: $data['judul'],
                body: $data['body'],
                kelasId: $kelas->id,
                sumber: 'guru',
            ));
            $sent++;
        }

        return redirect()->route('guru.pesan.form', ['kelas_id' => $kelas->id])
            ->with('success', "Pesan terkirim ke $sent siswa.");
    }

    /**
     * AJAX: ambil siswa kelas tertentu (untuk dropdown checklist).
     */
    public function ajaxSiswa(Request $r) {
        $kelas = Kelas::find($r->kelas_id);
        if (!$kelas || $kelas->guru_id !== auth()->id()) {
            return response()->json(['siswa' => []]);
        }
        $siswa = $kelas->siswa()->wherePivot('status','aktif')->get(['users.id','users.name','users.email']);
        return response()->json(['siswa' => $siswa]);
    }
}
