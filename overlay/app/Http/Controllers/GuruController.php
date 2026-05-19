<?php
namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Concerns\PerPage;

class GuruController extends Controller {
    use PerPage;

    public function index() {
        $guru = User::where('role','guru')->withCount('kelasDiampu')->orderBy('name')->paginate($this->perPage(20))->withQueryString();
        return view('guru.index', compact('guru'));
    }

    public function create() { return view('guru.form', ['guru' => new User(['role'=>'guru','aktif'=>true])]); }

    public function store(Request $request) {
        $data = $request->validate([
            'name' => 'required|string|max:120',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6',
            'nohp' => 'nullable|string|max:20',
            'aktif' => 'boolean',
        ]);
        $data['role'] = 'guru';
        $data['password'] = Hash::make($data['password']);
        $data['aktif'] = $request->boolean('aktif', true);
        User::create($data);
        return redirect()->route('guru.index')->with('success','Guru ditambahkan.');
    }

    public function edit(User $guru) {
        abort_unless($guru->role === 'guru', 404);
        return view('guru.form', compact('guru'));
    }

    public function update(Request $request, User $guru) {
        abort_unless($guru->role === 'guru', 404);
        $data = $request->validate([
            'name' => 'required|string|max:120',
            'email' => 'required|email|unique:users,email,' . $guru->id,
            'password' => 'nullable|min:6',
            'nohp' => 'nullable|string|max:20',
            'aktif' => 'boolean',
        ]);
        if (!empty($data['password'])) $data['password'] = Hash::make($data['password']);
        else unset($data['password']);
        $data['aktif'] = $request->boolean('aktif', false);
        $guru->update($data);
        return redirect()->route('guru.index')->with('success','Guru diperbarui.');
    }

    public function destroy(User $guru) {
        abort_unless($guru->role === 'guru', 404);
        $guru->delete();
        return back()->with('success','Guru dihapus.');
    }
}
