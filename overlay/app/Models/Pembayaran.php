<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Pembayaran extends Model {
    protected $table = 'pembayaran';
    protected $fillable = ['tagihan_id','tanggal_bayar','nominal','metode','referensi','catatan','admin_id'];
    protected $casts = ['nominal' => 'decimal:2', 'tanggal_bayar' => 'date'];

    public function tagihan(): BelongsTo { return $this->belongsTo(Tagihan::class); }
    public function admin(): BelongsTo { return $this->belongsTo(User::class, 'admin_id'); }

    protected static function booted(): void {
        static::saved(fn($p) => $p->tagihan?->recalcStatus());
        static::deleted(fn($p) => $p->tagihan?->recalcStatus());
    }
}
