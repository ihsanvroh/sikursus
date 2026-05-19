<?php
namespace App\Console\Commands;

use App\Models\PengingatLog;
use App\Models\PengingatSetting;
use App\Models\PengingatTemplate;
use App\Models\Tagihan;
use App\Notifications\TagihanReminder;
use Carbon\Carbon;
use Illuminate\Console\Command;

class SendAutoPengingat extends Command {
    protected $signature = 'pengingat:auto {--dry-run : Hanya simulasi, tidak kirim}';
    protected $description = 'Kirim pengingat otomatis berdasarkan setting & status tagihan';

    public function handle(): int {
        $dry = $this->option('dry-run');
        $today = Carbon::today();
        $totalSent = 0;
        $totalSkipped = 0;

        $settings = PengingatSetting::with('template')->where('aktif',true)
            ->whereHas('template', fn($t) => $t->where('aktif',true))->get();

        if ($settings->isEmpty()) {
            $this->info('Tidak ada setting aktif.');
            return self::SUCCESS;
        }

        foreach ($settings as $setting) {
            // Hitung target tanggal jatuh tempo
            $offset = (int) $setting->hari_offset;
            $targetTanggal = match($setting->trigger_type) {
                'before' => $today->copy()->addDays($offset),
                'on' => $today->copy(),
                'after' => $today->copy()->subDays($offset),
            };

            $tagihans = Tagihan::with('siswa','kelas')
                ->where('status','!=','lunas')
                ->whereDate('jatuh_tempo', $targetTanggal->toDateString())
                ->get();

            $this->line("Setting [{$setting->labelTrigger()}] target tanggal {$targetTanggal->toDateString()}: {$tagihans->count()} tagihan");

            foreach ($tagihans as $t) {
                // skip kalau sudah pernah dikirim untuk kombinasi siswa+tagihan+template hari ini
                $sudah = PengingatLog::where('siswa_id',$t->siswa_id)
                    ->where('tagihan_id',$t->id)
                    ->where('template_id',$setting->template_id)
                    ->whereDate('sent_at', $today->toDateString())
                    ->exists();
                if ($sudah) { $totalSkipped++; continue; }

                $tpl = $setting->template;
                $vars = [
                    'nama' => $t->siswa->name,
                    'kelas' => $t->kelas->nama,
                    'periode' => $t->periode,
                    'nominal' => number_format((float)$t->nominal, 0, ',', '.'),
                    'jatuh_tempo' => $t->jatuh_tempo->format('d M Y'),
                    'sisa' => number_format($t->sisaBayar(), 0, ',', '.'),
                ];
                $judul = PengingatTemplate::render($tpl->judul, $vars);
                $body = PengingatTemplate::render($tpl->body, $vars);

                if ($dry) {
                    $this->line("  [DRY] kirim ke {$t->siswa->name}: {$judul}");
                    continue;
                }
                try {
                    $t->siswa->notify(new TagihanReminder($judul, $body, $t->id));
                    PengingatLog::create([
                        'siswa_id' => $t->siswa_id, 'tagihan_id' => $t->id, 'template_id' => $tpl->id,
                        'channel' => 'in_app', 'mode' => 'auto',
                        'judul' => $judul, 'body' => $body, 'status' => 'terkirim', 'sent_at' => now(),
                    ]);
                    $totalSent++;
                } catch (\Throwable $e) {
                    PengingatLog::create([
                        'siswa_id' => $t->siswa_id, 'tagihan_id' => $t->id, 'template_id' => $tpl->id,
                        'channel' => 'in_app', 'mode' => 'auto',
                        'judul' => $judul, 'body' => $body, 'status' => 'gagal', 'error' => $e->getMessage(), 'sent_at' => now(),
                    ]);
                }
            }
        }

        $this->info("Selesai. Terkirim: {$totalSent}, Skipped (sudah dikirim hari ini): {$totalSkipped}");
        return self::SUCCESS;
    }
}
