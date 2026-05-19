<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Kelas extends Model {
    protected $table = 'kelas';
    protected $fillable = ['nama','kode','deskripsi','tarif','guru_id','aktif'];
    protected $casts = ['tarif' => 'decimal:2', 'aktif' => 'boolean'];

    public function guru(): BelongsTo {
        return $this->belongsTo(User::class, 'guru_id');
    }

    public function enrollments(): HasMany {
        return $this->hasMany(Enrollment::class, 'kelas_id');
    }

    public function siswa(): BelongsToMany {
        return $this->belongsToMany(User::class, 'enrollments', 'kelas_id', 'siswa_id')
            ->withPivot(['id','tgl_mulai','tgl_selesai','status'])
            ->withTimestamps();
    }

    public function siswaAktif(): BelongsToMany {
        return $this->siswa()->wherePivot('status', 'aktif');
    }

    public function jadwal(): HasMany {
        return $this->hasMany(JadwalKelas::class, 'kelas_id')
            ->orderBy('hari')->orderBy('jam_mulai');
    }
}
