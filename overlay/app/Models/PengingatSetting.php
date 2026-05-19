<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PengingatSetting extends Model {
    protected $table = 'pengingat_setting';
    protected $fillable = ['template_id','trigger_type','hari_offset','aktif'];
    protected $casts = ['aktif' => 'boolean'];

    public function template(): BelongsTo { return $this->belongsTo(PengingatTemplate::class, 'template_id'); }

    public function labelTrigger(): string {
        $h = abs((int)$this->hari_offset);
        return match($this->trigger_type) {
            'before' => "H-{$h} sebelum jatuh tempo",
            'on' => "Tepat di hari jatuh tempo",
            'after' => "H+{$h} setelah jatuh tempo",
        };
    }
}
