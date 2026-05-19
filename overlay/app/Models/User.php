<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class User extends Authenticatable {
    use HasFactory, Notifiable;

    protected $fillable = ['name','email','password','role','nohp','alamat','tgl_lahir','gender','foto','aktif'];
    protected $hidden = ['password','remember_token'];
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'tgl_lahir' => 'date',
        'aktif' => 'boolean',
    ];

    public function isAdmin(): bool { return $this->role === 'admin'; }
    public function isGuru(): bool { return $this->role === 'guru'; }
    public function isSiswa(): bool { return $this->role === 'siswa'; }

    // As guru
    public function kelasDiampu(): HasMany {
        return $this->hasMany(Kelas::class, 'guru_id');
    }

    // As siswa
    public function enrollments(): HasMany {
        return $this->hasMany(Enrollment::class, 'siswa_id');
    }

    public function kelasDiikuti(): BelongsToMany {
        return $this->belongsToMany(Kelas::class, 'enrollments', 'siswa_id', 'kelas_id')
            ->withPivot(['id','tgl_mulai','tgl_selesai','status','catatan'])
            ->withTimestamps();
    }

    public function tagihan(): HasMany {
        return $this->hasMany(Tagihan::class, 'siswa_id');
    }

    public function tagihanBelumLunas() {
        return $this->tagihan()->where('status', '!=', 'lunas');
    }
}
