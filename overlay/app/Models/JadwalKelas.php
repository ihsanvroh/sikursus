<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class JadwalKelas extends Model {
    protected $table = 'jadwal_kelas';
    protected $fillable = ['kelas_id','hari','jam_mulai','jam_selesai','aktif'];
    protected $casts = [
        'hari' => 'integer',
        'aktif' => 'boolean',
    ];

    public const HARI = [
        1 => 'Senin',
        2 => 'Selasa',
        3 => 'Rabu',
        4 => 'Kamis',
        5 => 'Jumat',
        6 => 'Sabtu',
        7 => 'Minggu',
    ];

    public function kelas(): BelongsTo {
        return $this->belongsTo(Kelas::class, 'kelas_id');
    }

    public function labelHari(): string {
        return self::HARI[$this->hari] ?? '?';
    }

    /**
     * Format: "Senin 14:00–15:00"
     */
    public function label(): string {
        return sprintf('%s %s–%s',
            $this->labelHari(),
            substr($this->jam_mulai, 0, 5),
            substr($this->jam_selesai, 0, 5),
        );
    }

    /**
     * Cek apakah dua slot bentrok.
     * Bentrok = hari sama DAN ada irisan waktu (open interval).
     */
    public static function bentrok(array $a, array $b): bool {
        if ((int)$a['hari'] !== (int)$b['hari']) return false;
        $aStart = $a['jam_mulai']; $aEnd = $a['jam_selesai'];
        $bStart = $b['jam_mulai']; $bEnd = $b['jam_selesai'];
        // overlap kalau aStart < bEnd dan bStart < aEnd
        return ($aStart < $bEnd) && ($bStart < $aEnd);
    }
}
