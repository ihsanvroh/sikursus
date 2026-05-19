<?php
namespace App\Http\Controllers;

use App\Models\Kelas;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Concerns\PerPage;

class KelasController extends Controller {
    use PerPage;

    public function index() {
        $kelas = Kelas::with('guru')->withCount(['siswaAktif as siswa_aktif_count'])->orderBy('nama')->paginate($this->perPage(20))->withQueryString();
        return view('kelas.index', compact('kelas'));
    }

    public function create() {
        $guru = User::where('role','guru')->where('aktif',true)->orderBy('name')->get();
        return view('kelas.form', ['kelas' => new Kelas(['aktif'=>true]), 'guru' => $guru]);
    }

    public function store(Request $request) {
        $data = $request->validate([
            'nama' => 'required|string|max:120',
            'kode' => 'required|string|max:20|unique:kelas,kode',
            'tarif' => 'required|numeric|min:0',
            'guru_id' => 'nullable|exists:users,id',
            'deskripsi' => 'nullable|string',
            'aktif' => 'boolean',
        ]);
        $data['aktif'] = $request->boolean('aktif', true);
        Kelas::create($data);
        return redirect()->route('kelas.index')->with('success','Kelas ditambahkan.');
    }

    public function show(Kelas $kela) {
        $kelas = $kela->load(['guru','siswa' => fn($q) => $q->wherePivot('status','aktif')]);
        return view('kelas.show', compact('kelas'));
    }

    public function edit(Kelas $kela) {
        $guru = User::where('role','guru')->where('aktif',true)->orderBy('name')->get();
        return view('kelas.form', ['kelas' => $kela, 'guru' => $guru]);
    }

    public function update(Request $request, Kelas $kela) {
        $data = $request->validate([
            'nama' => 'required|string|max:120',
            'kode' => 'required|string|max:20|unique:kelas,kode,' . $kela->id,
            'tarif' => 'required|numeric|min:0',
            'guru_id' => 'nullable|exists:users,id',
            'deskripsi' => 'nullable|string',
            'aktif' => 'boolean',
        ]);
        $data['aktif'] = $request->boolean('aktif', false);
        $kela->update($data);
        return redirect()->route('kelas.index')->with('success','Kelas diperbarui.');
    }

    public function destroy(Kelas $kela) {
        $kela->delete();
        return back()->with('success','Kelas dihapus.');
    }
}
