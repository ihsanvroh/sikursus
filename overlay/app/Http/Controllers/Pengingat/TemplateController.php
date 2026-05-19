<?php
namespace App\Http\Controllers\Pengingat;

use App\Http\Controllers\Controller;
use App\Models\PengingatTemplate;
use Illuminate\Http\Request;
use App\Http\Controllers\Concerns\PerPage;

class TemplateController extends Controller {
    use PerPage;

    public function index() {
        $template = PengingatTemplate::orderBy('nama')->paginate($this->perPage(20))->withQueryString();
        return view('pengingat.template.index', compact('template'));
    }
    public function create() { return view('pengingat.template.form', ['t' => new PengingatTemplate(['aktif'=>true])]); }
    public function store(Request $r) {
        $data = $r->validate(['nama'=>'required|string|max:120','judul'=>'required|string|max:200','body'=>'required|string','aktif'=>'boolean']);
        $data['aktif'] = $r->boolean('aktif', true);
        PengingatTemplate::create($data);
        return redirect()->route('pengingat.template.index')->with('success','Template dibuat.');
    }
    public function edit(PengingatTemplate $template) { return view('pengingat.template.form', ['t' => $template]); }
    public function update(Request $r, PengingatTemplate $template) {
        $data = $r->validate(['nama'=>'required|string|max:120','judul'=>'required|string|max:200','body'=>'required|string','aktif'=>'boolean']);
        $data['aktif'] = $r->boolean('aktif', false);
        $template->update($data);
        return redirect()->route('pengingat.template.index')->with('success','Template diperbarui.');
    }
    public function destroy(PengingatTemplate $template) {
        $template->delete();
        return back()->with('success','Template dihapus.');
    }
}
