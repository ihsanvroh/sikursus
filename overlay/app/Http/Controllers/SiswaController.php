<?php
namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Kelas;
use App\Models\Enrollment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;
use App\Http\Controllers\Concerns\PerPage;
use App\Services\JadwalConflictChecker;

class SiswaController extends Controller {
    use PerPage;

    public function index(Request $request) {
        $q = User::where('role','siswa');
        if ($request->filled('q')) {
            $kw = $request->q;
            $q->where(fn($x) => $x->where('name','like',"%$kw%")->orWhere('email','like',"%$kw%")->orWhere('nohp','like',"%$kw%"));
        }
        if ($request->filled('kelas_id')) {
            $kid = $request->kelas_id;
            $q->whereHas('enrollments', fn($e) => $e->where('kelas_id',$kid)->where('status','aktif'));
        }
        $siswa = $q->withCount(['enrollments as enrollments_aktif' => fn($e) => $e->where('status','aktif')])
                   ->orderBy('name')->paginate($this->perPage(20))->withQueryString();
        $kelas = Kelas::where('aktif',true)->orderBy('nama')->get();
        return view('siswa.index', compact('siswa','kelas'));
    }

    public function create() {
        return view('siswa.form', ['siswa' => new User(['role'=>'siswa','aktif'=>true])]);
    }

    public function store(Request $request) {
        $data = $request->validate([
            'name' => 'required|string|max:120',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6',
            'nohp' => 'nullable|string|max:20',
            'gender' => 'nullable|in:L,P',
            'tgl_lahir' => 'nullable|date',
            'alamat' => 'nullable|string',
            'aktif' => 'boolean',
        ]);
        $data['role'] = 'siswa';
        $data['password'] = Hash::make($data['password']);
        $data['aktif'] = $request->boolean('aktif', true);
        User::create($data);
        return redirect()->route('siswa.index')->with('success','Siswa ditambahkan.');
    }

    public function show(User $siswa) {
        abort_unless($siswa->role === 'siswa', 404);
        $enrollments = $siswa->enrollments()->with(['kelas.guru'])->latest()->get();
        $tagihan = $siswa->tagihan()->with(['kelas','pembayaran'])->latest('periode')->get();
        $kelasTersedia = Kelas::where('aktif',true)
            ->whereNotIn('id', $siswa->enrollments()->where('status','aktif')->pluck('kelas_id'))
            ->orderBy('nama')->get();
        return view('siswa.show', compact('siswa','enrollments','tagihan','kelasTersedia'));
    }

    public function edit(User $siswa) {
        abort_unless($siswa->role === 'siswa', 404);
        return view('siswa.form', compact('siswa'));
    }

    public function update(Request $request, User $siswa) {
        abort_unless($siswa->role === 'siswa', 404);
        $data = $request->validate([
            'name' => 'required|string|max:120',
            'email' => 'required|email|unique:users,email,' . $siswa->id,
            'password' => 'nullable|min:6',
            'nohp' => 'nullable|string|max:20',
            'gender' => 'nullable|in:L,P',
            'tgl_lahir' => 'nullable|date',
            'alamat' => 'nullable|string',
            'aktif' => 'boolean',
        ]);
        if (!empty($data['password'])) $data['password'] = Hash::make($data['password']);
        else unset($data['password']);
        $data['aktif'] = $request->boolean('aktif', false);
        $siswa->update($data);
        return redirect()->route('siswa.show', $siswa)->with('success','Data siswa diperbarui.');
    }

    public function destroy(User $siswa) {
        abort_unless($siswa->role === 'siswa', 404);
        $siswa->delete();
        return redirect()->route('siswa.index')->with('success','Siswa dihapus.');
    }

    // Enrollment management
    public function tambahKelas(Request $request, User $siswa, JadwalConflictChecker $checker) {
        abort_unless($siswa->role === 'siswa', 404);
        $data = $request->validate([
            'kelas_id' => 'required|exists:kelas,id',
            'tgl_mulai' => 'nullable|date',
            'catatan' => 'nullable|string',
        ]);
        $exists = Enrollment::where('siswa_id',$siswa->id)->where('kelas_id',$data['kelas_id'])->where('status','aktif')->exists();
        if ($exists) return back()->with('error','Siswa sudah terdaftar aktif di kelas ini.');

        // Cek bentrok jadwal
        $conflicts = $checker->cekSiswa($siswa->id, (int)$data['kelas_id']);
        if (!empty($conflicts)) {
            return back()->with('error', 'Tidak bisa enroll: ' . $checker->formatPesanSiswa($conflicts));
        }

        Enrollment::create([
            'siswa_id' => $siswa->id,
            'kelas_id' => $data['kelas_id'],
            'tgl_mulai' => $data['tgl_mulai'] ?? now(),
            'status' => 'aktif',
            'catatan' => $data['catatan'] ?? null,
        ]);
        return back()->with('success','Siswa ditambahkan ke kelas.');
    }

    public function gantiKelas(Request $request, Enrollment $enrollment, JadwalConflictChecker $checker) {
        $data = $request->validate([
            'kelas_baru_id' => 'required|exists:kelas,id',
            'tgl_pindah' => 'nullable|date',
            'catatan' => 'nullable|string',
        ]);
        if ($enrollment->kelas_id == $data['kelas_baru_id']) {
            return back()->with('error','Kelas baru sama dengan kelas saat ini.');
        }

        // Cek bentrok kelas baru vs kelas lain yang masih aktif (selain kelas yang akan diganti)
        $conflicts = $checker->cekSiswa($enrollment->siswa_id, (int)$data['kelas_baru_id']);
        // Filter: drop conflict yang berasal dari enrollment ini sendiri (karena akan di-tutup)
        $conflicts = array_filter($conflicts, fn($c) => $c['kelas_lama']->id !== $enrollment->kelas_id);
        if (!empty($conflicts)) {
            return back()->with('error', 'Tidak bisa pindah kelas: ' . $checker->formatPesanSiswa(array_values($conflicts)));
        }

        $tgl = $data['tgl_pindah'] ?? now()->toDateString();
        $enrollment->update(['status'=>'berhenti','tgl_selesai'=>$tgl,'catatan'=>($enrollment->catatan ?? '') . "\n[Pindah ke kelas lain pada $tgl]"]);
        Enrollment::create([
            'siswa_id' => $enrollment->siswa_id,
            'kelas_id' => $data['kelas_baru_id'],
            'tgl_mulai' => $tgl,
            'status' => 'aktif',
            'catatan' => $data['catatan'] ?? null,
        ]);
        return back()->with('success','Siswa berhasil pindah kelas.');
    }

    public function selesaikanEnrollment(Enrollment $enrollment) {
        $enrollment->update(['status'=>'selesai','tgl_selesai'=>now()]);
        return back()->with('success','Enrollment ditandai selesai.');
    }
}
