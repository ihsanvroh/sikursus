<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PengingatLog extends Model {
    protected $table = 'pengingat_log';
    protected $fillable = ['siswa_id','tagihan_id','template_id','channel','mode','judul','body','status','error','sent_at'];
    protected $casts = ['sent_at' => 'datetime'];

    public function siswa(): BelongsTo { return $this->belongsTo(User::class, 'siswa_id'); }
    public function tagihan(): BelongsTo { return $this->belongsTo(Tagihan::class); }
    public function template(): BelongsTo { return $this->belongsTo(PengingatTemplate::class, 'template_id'); }
}
