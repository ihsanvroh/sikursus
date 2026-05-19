<?php
namespace App\Http\Controllers;

use App\Models\Kelas;
use App\Models\User;
use App\Models\JadwalKelas;
use App\Services\JadwalConflictChecker;
use Illuminate\Http\Request;
use App\Http\Controllers\Concerns\PerPage;
use Illuminate\Support\Facades\DB;

class KelasController extends Controller {
    use PerPage;

    public function index() {
        $kelas = Kelas::with(['guru','jadwal'])
            ->withCount(['siswaAktif as siswa_aktif_count'])
            ->orderBy('nama')
            ->paginate($this->perPage(20))
            ->withQueryString();
        return view('kelas.index', compact('kelas'));
    }

    public function create() {
        $guru = User::where('role','guru')->where('aktif',true)->orderBy('name')->get();
        return view('kelas.form', ['kelas' => new Kelas(['aktif'=>true]), 'guru' => $guru]);
    }

    public function store(Request $request, JadwalConflictChecker $checker) {
        $data = $this->validateMain($request);
        $jadwal = $this->extractJadwal($request);

        // Cek bentrok dgn kelas lain yang dipegang guru sama
        if (!empty($jadwal) && !empty($data['guru_id'])) {
            $conflicts = $checker->cekGuru($data['guru_id'], null, $jadwal);
            if (!empty($conflicts)) {
                return back()->withInput()
                    ->with('error', 'Jadwal bentrok untuk guru: ' . $checker->formatPesanGuru($conflicts));
            }
        }

        DB::transaction(function() use ($data, $jadwal) {
            $kelas = Kelas::create($data);
            foreach ($jadwal as $j) {
                $kelas->jadwal()->create($j);
            }
        });

        return redirect()->route('kelas.index')->with('success','Kelas ditambahkan.');
    }

    public function show(Kelas $kela) {
        $kelas = $kela->load(['guru','siswa' => fn($q) => $q->wherePivot('status','aktif'), 'jadwal']);
        return view('kelas.show', compact('kelas'));
    }

    public function edit(Kelas $kela) {
        $guru = User::where('role','guru')->where('aktif',true)->orderBy('name')->get();
        $kela->load('jadwal');
        return view('kelas.form', ['kelas' => $kela, 'guru' => $guru]);
    }

    public function update(Request $request, Kelas $kela, JadwalConflictChecker $checker) {
        $data = $this->validateMain($request, $kela->id);
        $jadwal = $this->extractJadwal($request);

        if (!empty($jadwal) && !empty($data['guru_id'])) {
            $conflicts = $checker->cekGuru($data['guru_id'], $kela->id, $jadwal);
            if (!empty($conflicts)) {
                return back()->withInput()
                    ->with('error', 'Jadwal bentrok untuk guru: ' . $checker->formatPesanGuru($conflicts));
            }
        }

        DB::transaction(function() use ($kela, $data, $jadwal) {
            $kela->update($data);
            // Strategi simple: replace semua jadwal
            $kela->jadwal()->delete();
            foreach ($jadwal as $j) {
                $kela->jadwal()->create($j);
            }
        });

        return redirect()->route('kelas.index')->with('success','Kelas diperbarui.');
    }

    public function destroy(Kelas $kela) {
        $kela->delete();
        return back()->with('success','Kelas dihapus.');
    }

    private function validateMain(Request $request, ?int $excludeId = null): array {
        $unique = 'unique:kelas,kode' . ($excludeId ? ",$excludeId" : '');
        $data = $request->validate([
            'nama' => 'required|string|max:120',
            'kode' => "required|string|max:20|$unique",
            'tarif' => 'required|numeric|min:0',
            'guru_id' => 'nullable|exists:users,id',
            'deskripsi' => 'nullable|string',
            'aktif' => 'boolean',
        ]);
        $data['aktif'] = $request->boolean('aktif', false);
        return $data;
    }

    /**
     * Ambil array jadwal dari request — drop row yang gak lengkap.
     * Format input dari form:
     *   jadwal[0][hari], jadwal[0][jam_mulai], jadwal[0][jam_selesai]
     */
    private function extractJadwal(Request $request): array {
        $rows = $request->input('jadwal', []);
        $clean = [];
        foreach ($rows as $r) {
            $hari = $r['hari'] ?? null;
            $start = $r['jam_mulai'] ?? null;
            $end = $r['jam_selesai'] ?? null;
            if ($hari && $start && $end) {
                // Normalisasi ke HH:MM:SS biar konsisten dgn DB
                $start = strlen($start) === 5 ? "$start:00" : $start;
                $end = strlen($end) === 5 ? "$end:00" : $end;
                if ($start >= $end) continue; // skip invalid
                $clean[] = [
                    'hari' => (int)$hari,
                    'jam_mulai' => $start,
                    'jam_selesai' => $end,
                    'aktif' => true,
                ];
            }
        }
        return $clean;
    }
}
