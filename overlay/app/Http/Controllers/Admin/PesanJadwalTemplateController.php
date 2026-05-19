<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PesanJadwalTemplate;
use App\Http\Controllers\Concerns\PerPage;
use Illuminate\Http\Request;

class PesanJadwalTemplateController extends Controller {
    use PerPage;

    public function index() {
        $template = PesanJadwalTemplate::orderBy('nama')->paginate($this->perPage(20))->withQueryString();
        return view('admin.pesan_template.index', compact('template'));
    }

    public function create() {
        return view('admin.pesan_template.form', ['t' => new PesanJadwalTemplate(['aktif'=>true])]);
    }

    public function store(Request $r) {
        $data = $r->validate(['nama'=>'required|string|max:120','judul'=>'required|string|max:200','body'=>'required|string','aktif'=>'boolean']);
        $data['aktif'] = $r->boolean('aktif', true);
        PesanJadwalTemplate::create($data);
        return redirect()->route('admin.pesan_template.index')->with('success','Template dibuat.');
    }

    public function edit(PesanJadwalTemplate $template) {
        return view('admin.pesan_template.form', ['t' => $template]);
    }

    public function update(Request $r, PesanJadwalTemplate $template) {
        $data = $r->validate(['nama'=>'required|string|max:120','judul'=>'required|string|max:200','body'=>'required|string','aktif'=>'boolean']);
        $data['aktif'] = $r->boolean('aktif', false);
        $template->update($data);
        return redirect()->route('admin.pesan_template.index')->with('success','Template diperbarui.');
    }

    public function destroy(PesanJadwalTemplate $template) {
        $template->delete();
        return back()->with('success','Template dihapus.');
    }
}
