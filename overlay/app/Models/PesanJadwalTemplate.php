<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PesanJadwalTemplate extends Model {
    protected $table = 'pesan_jadwal_template';
    protected $fillable = ['nama','judul','body','aktif'];
    protected $casts = ['aktif' => 'boolean'];

    /**
     * Render template dengan variabel.
     * Vars: {nama_siswa}, {kelas}, {hari}, {jam}, {tanggal}
     */
    public function render(array $vars): array {
        $replace = function(string $str) use ($vars) {
            foreach ($vars as $k => $v) {
                $str = str_replace('{' . $k . '}', (string)$v, $str);
            }
            return $str;
        };
        return [
            'judul' => $replace($this->judul),
            'body' => $replace($this->body),
        ];
    }
}
