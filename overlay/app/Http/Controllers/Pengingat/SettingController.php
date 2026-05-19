<?php
namespace App\Http\Controllers\Pengingat;

use App\Http\Controllers\Controller;
use App\Models\PengingatSetting;
use App\Models\PengingatTemplate;
use Illuminate\Http\Request;

class SettingController extends Controller {
    public function index() {
        $settings = PengingatSetting::with('template')->orderBy('trigger_type')->orderBy('hari_offset')->get();
        $templates = PengingatTemplate::where('aktif',true)->orderBy('nama')->get();
        return view('pengingat.setting.index', compact('settings','templates'));
    }
    public function store(Request $r) {
        $data = $r->validate([
            'template_id' => 'required|exists:pengingat_template,id',
            'trigger_type' => 'required|in:before,on,after',
            'hari_offset' => 'nullable|integer|min:0|max:365',
            'aktif' => 'boolean',
        ]);
        $data['hari_offset'] = (int) ($data['hari_offset'] ?? 0);
        $data['aktif'] = $r->boolean('aktif', true);
        if ($data['trigger_type'] === 'on') $data['hari_offset'] = 0;
        PengingatSetting::create($data);
        return back()->with('success','Aturan ditambahkan.');
    }
    public function update(Request $r, PengingatSetting $setting) {
        $data = $r->validate([
            'template_id' => 'required|exists:pengingat_template,id',
            'trigger_type' => 'required|in:before,on,after',
            'hari_offset' => 'nullable|integer|min:0|max:365',
            'aktif' => 'boolean',
        ]);
        $data['hari_offset'] = (int) ($data['hari_offset'] ?? 0);
        $data['aktif'] = $r->boolean('aktif', false);
        if ($data['trigger_type'] === 'on') $data['hari_offset'] = 0;
        $setting->update($data);
        return back()->with('success','Aturan diperbarui.');
    }
    public function destroy(PengingatSetting $setting) {
        $setting->delete();
        return back()->with('success','Aturan dihapus.');
    }
}
