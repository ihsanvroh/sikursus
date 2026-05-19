<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JadwalNotifSetting extends Model {
    protected $table = 'jadwal_notif_setting';
    protected $fillable = ['hari_offset','jam_kirim','aktif','keterangan'];
    protected $casts = [
        'hari_offset' => 'integer',
        'aktif' => 'boolean',
    ];

    public function labelOffset(): string {
        return match(true) {
            $this->hari_offset === 0 => 'Hari H',
            $this->hari_offset > 0 => "H-{$this->hari_offset}",
            default => 'H+' . abs($this->hari_offset),
        };
    }
}
