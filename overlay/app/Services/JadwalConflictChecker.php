<?php
namespace App\Services;

use App\Models\JadwalKelas;
use App\Models\Kelas;
use App\Models\User;
use Illuminate\Support\Collection;

/**
 * Cek bentrok jadwal:
 * - Antar kelas yang dipegang guru sama (saat assign guru ke kelas, atau ubah jadwal)
 * - Antar kelas yang diikuti siswa sama (saat enroll siswa ke kelas baru)
 */
class JadwalConflictChecker {

    /**
     * Cek apakah jadwal-jadwal baru untuk satu kelas akan bentrok dengan kelas lain
     * yang dipegang guru tertentu (kecuali kelas itu sendiri).
     *
     * @param int|null $guruId  guru yang akan pegang kelas ini
     * @param int|null $excludeKelasId  exclude kelas ini sendiri saat update
     * @param array $jadwalBaru  array of ['hari'=>int, 'jam_mulai'=>'HH:MM:SS', 'jam_selesai'=>'HH:MM:SS']
     * @return array  list bentrok, format: [{kelas: Kelas, slot_baru: array, slot_lama: JadwalKelas}, ...]
     */
    public function cekGuru(?int $guruId, ?int $excludeKelasId, array $jadwalBaru): array {
        if (!$guruId || empty($jadwalBaru)) return [];

        $kelasLain = Kelas::query()
            ->where('guru_id', $guruId)
            ->when($excludeKelasId, fn($q) => $q->where('id','!=',$excludeKelasId))
            ->where('aktif', true)
            ->with(['jadwal' => fn($q) => $q->where('aktif', true)])
            ->get();

        $conflicts = [];
        foreach ($kelasLain as $k) {
            foreach ($k->jadwal as $jLama) {
                foreach ($jadwalBaru as $jBaru) {
                    if (JadwalKelas::bentrok($jBaru, [
                        'hari' => $jLama->hari,
                        'jam_mulai' => $jLama->jam_mulai,
                        'jam_selesai' => $jLama->jam_selesai,
                    ])) {
                        $conflicts[] = [
                            'kelas' => $k,
                            'slot_baru' => $jBaru,
                            'slot_lama' => $jLama,
                        ];
                    }
                }
            }
        }
        return $conflicts;
    }

    /**
     * Cek apakah jadwal kelas yang akan diikuti siswa bentrok dengan kelas lain
     * yang sedang aktif diikuti siswa.
     *
     * @param int $siswaId
     * @param int $kelasBaruId  kelas yang akan diikuti siswa
     * @return array list bentrok
     */
    public function cekSiswa(int $siswaId, int $kelasBaruId): array {
        $siswa = User::with(['kelasDiikuti' => function($q){
            $q->wherePivot('status','aktif')->with(['jadwal' => fn($jq) => $jq->where('aktif',true)]);
        }])->find($siswaId);
        if (!$siswa) return [];

        $kelasBaru = Kelas::with(['jadwal' => fn($q) => $q->where('aktif',true)])->find($kelasBaruId);
        if (!$kelasBaru) return [];

        $conflicts = [];
        foreach ($siswa->kelasDiikuti as $kLama) {
            if ($kLama->id === $kelasBaru->id) continue;
            foreach ($kLama->jadwal as $jLama) {
                foreach ($kelasBaru->jadwal as $jBaru) {
                    if (JadwalKelas::bentrok(
                        ['hari'=>$jLama->hari,'jam_mulai'=>$jLama->jam_mulai,'jam_selesai'=>$jLama->jam_selesai],
                        ['hari'=>$jBaru->hari,'jam_mulai'=>$jBaru->jam_mulai,'jam_selesai'=>$jBaru->jam_selesai],
                    )) {
                        $conflicts[] = [
                            'kelas_lama' => $kLama,
                            'slot_lama' => $jLama,
                            'kelas_baru' => $kelasBaru,
                            'slot_baru' => $jBaru,
                        ];
                    }
                }
            }
        }
        return $conflicts;
    }

    /**
     * Format pesan error human-readable dari array conflicts (cekGuru).
     */
    public function formatPesanGuru(array $conflicts): string {
        $msgs = [];
        foreach ($conflicts as $c) {
            $msgs[] = sprintf(
                'Bentrok dengan kelas "%s" (%s)',
                $c['kelas']->nama,
                $c['slot_lama']->label(),
            );
        }
        return implode('; ', array_unique($msgs));
    }

    /**
     * Format pesan error dari array conflicts (cekSiswa).
     */
    public function formatPesanSiswa(array $conflicts): string {
        $msgs = [];
        foreach ($conflicts as $c) {
            $msgs[] = sprintf(
                'Bentrok dengan kelas "%s" (%s)',
                $c['kelas_lama']->nama,
                $c['slot_lama']->label(),
            );
        }
        return implode('; ', array_unique($msgs));
    }
}
