<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PengingatTemplate extends Model {
    protected $table = 'pengingat_template';
    protected $fillable = ['nama','judul','body','aktif'];
    protected $casts = ['aktif' => 'boolean'];

    public function settings() { return $this->hasMany(PengingatSetting::class, 'template_id'); }

    public static function render(string $tpl, array $vars): string {
        foreach ($vars as $k => $v) {
            $tpl = str_replace('{' . $k . '}', (string)$v, $tpl);
        }
        return $tpl;
    }
}
