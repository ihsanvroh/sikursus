<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Tagihan extends Model {
    protected $table = 'tagihan';
    protected $fillable = ['siswa_id','kelas_id','enrollment_id','periode','nominal','terbayar','jatuh_tempo','status','keterangan'];
    protected $casts = ['nominal' => 'decimal:2', 'terbayar' => 'decimal:2', 'jatuh_tempo' => 'date'];

    public function siswa(): BelongsTo { return $this->belongsTo(User::class, 'siswa_id'); }
    public function kelas(): BelongsTo { return $this->belongsTo(Kelas::class, 'kelas_id'); }
    public function enrollment(): BelongsTo { return $this->belongsTo(Enrollment::class); }
    public function pembayaran(): HasMany { return $this->hasMany(Pembayaran::class); }

    public function recalcStatus(): void {
        $total = (float) $this->pembayaran()->sum('nominal');
        $this->terbayar = $total;
        if ($total <= 0) $this->status = 'belum_bayar';
        elseif ($total >= (float) $this->nominal) $this->status = 'lunas';
        else $this->status = 'sebagian';
        $this->save();
    }

    public function sisaBayar(): float {
        return max(0, (float)$this->nominal - (float)$this->terbayar);
    }
}
