<?php
namespace App\Http\Controllers\Pengingat;

use App\Http\Controllers\Controller;
use App\Models\PengingatLog;
use App\Models\PengingatTemplate;
use App\Models\Tagihan;
use App\Models\User;
use App\Notifications\TagihanReminder;
use Illuminate\Http\Request;

class KirimController extends Controller {
    public function form() {
        $siswa = User::where('role','siswa')->where('aktif',true)->orderBy('name')->get();
        $templates = PengingatTemplate::where('aktif',true)->orderBy('nama')->get();
        return view('pengingat.kirim.form', compact('siswa','templates'));
    }

    public function send(Request $r) {
        $data = $r->validate([
            'siswa_id' => 'required|exists:users,id',
            'tagihan_id' => 'nullable|exists:tagihan,id',
            'template_id' => 'required|exists:pengingat_template,id',
        ]);
        $siswa = User::findOrFail($data['siswa_id']);
        $tpl = PengingatTemplate::findOrFail($data['template_id']);
        $tagihan = $data['tagihan_id'] ? Tagihan::find($data['tagihan_id']) : null;

        $vars = [
            'nama' => $siswa->name,
            'kelas' => $tagihan?->kelas?->nama ?? '-',
            'periode' => $tagihan?->periode ?? '-',
            'nominal' => $tagihan ? number_format((float)$tagihan->nominal, 0, ',', '.') : '-',
            'jatuh_tempo' => $tagihan?->jatuh_tempo?->format('d M Y') ?? '-',
            'sisa' => $tagihan ? number_format($tagihan->sisaBayar(), 0, ',', '.') : '-',
        ];
        $judul = PengingatTemplate::render($tpl->judul, $vars);
        $body = PengingatTemplate::render($tpl->body, $vars);

        try {
            $siswa->notify(new TagihanReminder($judul, $body, $tagihan?->id));
            PengingatLog::create([
                'siswa_id' => $siswa->id, 'tagihan_id' => $tagihan?->id, 'template_id' => $tpl->id,
                'channel' => 'in_app', 'mode' => 'manual',
                'judul' => $judul, 'body' => $body, 'status' => 'terkirim', 'sent_at' => now(),
            ]);
            return back()->with('success','Pengingat terkirim ke ' . $siswa->name);
        } catch (\Throwable $e) {
            PengingatLog::create([
                'siswa_id' => $siswa->id, 'tagihan_id' => $tagihan?->id, 'template_id' => $tpl->id,
                'channel' => 'in_app', 'mode' => 'manual',
                'judul' => $judul, 'body' => $body, 'status' => 'gagal', 'error' => $e->getMessage(), 'sent_at' => now(),
            ]);
            return back()->with('error','Gagal kirim: ' . $e->getMessage());
        }
    }

    public function ajaxTagihan(Request $r) {
        $r->validate(['siswa_id' => 'required|exists:users,id']);
        $tagihan = Tagihan::with('kelas')->where('siswa_id',$r->siswa_id)->where('status','!=','lunas')->orderByDesc('periode')->get();
        return response()->json($tagihan->map(fn($t) => [
            'id' => $t->id,
            'label' => "[{$t->periode}] {$t->kelas->nama} - Rp ".number_format((float)$t->nominal,0,',','.')." (jatuh tempo {$t->jatuh_tempo->format('d M Y')})"
        ]));
    }
}
