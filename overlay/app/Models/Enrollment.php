<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Enrollment extends Model {
    protected $fillable = ['siswa_id','kelas_id','tgl_mulai','tgl_selesai','status','catatan'];
    protected $casts = ['tgl_mulai' => 'date', 'tgl_selesai' => 'date'];

    public function siswa(): BelongsTo { return $this->belongsTo(User::class, 'siswa_id'); }
    public function kelas(): BelongsTo { return $this->belongsTo(Kelas::class, 'kelas_id'); }
}
