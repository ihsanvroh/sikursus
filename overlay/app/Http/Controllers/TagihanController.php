<?php
namespace App\Http\Controllers;

use App\Models\Tagihan;
use App\Models\Enrollment;
use App\Models\Kelas;
use App\Models\User;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Http\Controllers\Concerns\PerPage;

class TagihanController extends Controller {
    use PerPage;

    public function index(Request $request) {
        $q = Tagihan::with(['siswa','kelas']);
        if ($request->filled('status')) $q->where('status', $request->status);
        if ($request->filled('periode')) $q->where('periode', $request->periode);
        if ($request->filled('q')) {
            $kw = $request->q;
            $q->whereHas('siswa', fn($s) => $s->where('name','like',"%$kw%"));
        }
        $tagihan = $q->orderByDesc('periode')->orderBy('jatuh_tempo')->paginate($this->perPage(25))->withQueryString();
        $periodes = Tagihan::select('periode')->distinct()->orderByDesc('periode')->pluck('periode');
        return view('tagihan.index', compact('tagihan','periodes'));
    }

    public function show(Tagihan $tagihan) {
        $tagihan->load(['siswa','kelas','pembayaran.admin']);
        return view('tagihan.show', compact('tagihan'));
    }

    public function create() {
        $siswa = User::where('role','siswa')->orderBy('name')->get();
        $kelas = Kelas::where('aktif',true)->orderBy('nama')->get();
        return view('tagihan.form', ['tagihan' => new Tagihan(['periode'=>now()->format('Y-m'),'jatuh_tempo'=>now()->day(10)->toDateString()]), 'siswa' => $siswa, 'kelas' => $kelas]);
    }

    public function store(Request $request) {
        $data = $request->validate([
            'siswa_id' => 'required|exists:users,id',
            'kelas_id' => 'required|exists:kelas,id',
            'periode' => 'required|regex:/^\d{4}-\d{2}$/',
            'nominal' => 'required|numeric|min:0',
            'jatuh_tempo' => 'required|date',
            'keterangan' => 'nullable|string',
        ]);
        $exists = Tagihan::where('siswa_id',$data['siswa_id'])->where('kelas_id',$data['kelas_id'])->where('periode',$data['periode'])->exists();
        if ($exists) return back()->withInput()->with('error','Tagihan untuk kombinasi siswa+kelas+periode sudah ada.');
        $en = Enrollment::where('siswa_id',$data['siswa_id'])->where('kelas_id',$data['kelas_id'])->latest()->first();
        $data['enrollment_id'] = $en?->id;
        $data['status'] = 'belum_bayar';
        Tagihan::create($data);
        return redirect()->route('tagihan.index')->with('success','Tagihan dibuat.');
    }

    public function destroy(Tagihan $tagihan) {
        $tagihan->delete();
        return back()->with('success','Tagihan dihapus.');
    }

    // Generate tagihan untuk semua enrollment aktif untuk periode tertentu
    public function generate(Request $request) {
        $data = $request->validate([
            'periode' => 'required|regex:/^\d{4}-\d{2}$/',
            'tgl_jatuh_tempo' => 'required|date',
        ]);
        $periode = $data['periode'];
        $jt = $data['tgl_jatuh_tempo'];
        $created = 0;
        $skipped = 0;
        foreach (Enrollment::with('kelas')->where('status','aktif')->get() as $e) {
            $exist = Tagihan::where('siswa_id',$e->siswa_id)->where('kelas_id',$e->kelas_id)->where('periode',$periode)->exists();
            if ($exist) { $skipped++; continue; }
            Tagihan::create([
                'siswa_id' => $e->siswa_id,
                'kelas_id' => $e->kelas_id,
                'enrollment_id' => $e->id,
                'periode' => $periode,
                'nominal' => $e->kelas->tarif,
                'jatuh_tempo' => $jt,
                'status' => 'belum_bayar',
            ]);
            $created++;
        }
        return back()->with('success', "Generate selesai: $created baru, $skipped sudah ada.");
    }
}
