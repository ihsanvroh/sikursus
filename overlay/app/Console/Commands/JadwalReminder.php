<?php
namespace App\Console\Commands;

use App\Models\JadwalKelas;
use App\Models\JadwalNotifSetting;
use App\Models\Kelas;
use App\Notifications\PesanJadwalNotification;
use Carbon\Carbon;
use Illuminate\Console\Command;

class JadwalReminder extends Command {
    protected $signature = 'jadwal:reminder {--dry-run : tidak kirim, hanya tampilkan}';
    protected $description = 'Kirim notif H-N reminder jadwal kelas ke siswa berdasarkan setting aktif';

    public function handle(): int {
        $now = Carbon::now();
        $today = $now->copy()->startOfDay();
        $hhmm = $now->format('H:i');
        $force = $this->option('dry-run'); // pas dry-run, kirim ke screen aja, abaikan jam

        $settings = JadwalNotifSetting::where('aktif', true)->get();
        if ($settings->isEmpty()) {
            $this->info('Tidak ada setting aktif.');
            return 0;
        }

        $sent = 0; $skipped = 0;

        foreach ($settings as $s) {
            // Cek jam kirim — cuma fire saat menit yg sama
            $jamSetting = substr($s->jam_kirim, 0, 5); // "19:00:00" → "19:00"
            if (!$force && $jamSetting !== $hhmm) {
                continue;
            }

            // Tanggal target = hari ini + hari_offset
            $targetDate = $today->copy()->addDays($s->hari_offset);
            $hari = (int)$targetDate->isoWeekday(); // 1=Senin..7=Minggu

            $jadwalList = JadwalKelas::with(['kelas.guru','kelas.siswa' => fn($q) => $q->wherePivot('status','aktif')])
                ->where('aktif', true)
                ->where('hari', $hari)
                ->get();

            foreach ($jadwalList as $j) {
                $kelas = $j->kelas;
                if (!$kelas || !$kelas->aktif) { $skipped++; continue; }
                foreach ($kelas->siswa as $siswa) {
                    $offsetLabel = $s->labelOffset();
                    $judul = sprintf('%s · Kelas %s pada %s',
                        $offsetLabel,
                        $kelas->nama,
                        $targetDate->translatedFormat('l, d F Y'),
                    );
                    $body = sprintf(
                        "Halo %s,\n\nIngat kelas %s:\n%s pukul %s–%s\nGuru: %s\n\nJangan lupa hadir tepat waktu ya.",
                        $siswa->name,
                        $kelas->nama,
                        $j->labelHari(),
                        substr($j->jam_mulai,0,5),
                        substr($j->jam_selesai,0,5),
                        $kelas->guru->name ?? '-',
                    );

                    if ($this->option('dry-run')) {
                        $this->line("[DRY] → {$siswa->email} :: $judul");
                    } else {
                        $siswa->notify(new PesanJadwalNotification(
                            judul: $judul,
                            body: $body,
                            kelasId: $kelas->id,
                            sumber: 'system',
                        ));
                    }
                    $sent++;
                }
            }
        }

        $this->info("Selesai. Sent: $sent, skipped: $skipped" . ($this->option('dry-run') ? ' (DRY-RUN)' : ''));
        return 0;
    }
}
