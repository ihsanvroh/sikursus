<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\JadwalNotifSetting;
use Illuminate\Http\Request;

class JadwalNotifSettingController extends Controller {
    public function index() {
        $settings = JadwalNotifSetting::orderBy('hari_offset','desc')->get();
        return view('admin.jadwal_notif.index', compact('settings'));
    }

    public function store(Request $r) {
        $data = $r->validate([
            'hari_offset' => 'required|integer|min:-30|max:30',
            'jam_kirim' => 'required',
            'aktif' => 'boolean',
            'keterangan' => 'nullable|string|max:200',
        ]);
        $data['aktif'] = $r->boolean('aktif', true);
        JadwalNotifSetting::create($data);
        return back()->with('success','Aturan ditambahkan.');
    }

    public function update(Request $r, JadwalNotifSetting $setting) {
        $data = $r->validate([
            'hari_offset' => 'required|integer|min:-30|max:30',
            'jam_kirim' => 'required',
            'aktif' => 'boolean',
            'keterangan' => 'nullable|string|max:200',
        ]);
        $data['aktif'] = $r->boolean('aktif', false);
        $setting->update($data);
        return back()->with('success','Aturan diperbarui.');
    }

    public function destroy(JadwalNotifSetting $setting) {
        $setting->delete();
        return back()->with('success','Aturan dihapus.');
    }
}
