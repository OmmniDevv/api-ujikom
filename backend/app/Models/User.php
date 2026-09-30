<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name', 'email', 'password', 'role', 'no_hp', 'alamat', 'foto_profile', 'skor_reputasi',
    ];

    protected $hidden = ['password', 'remember_token'];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function peminjaman(): HasMany
    {
        return $this->hasMany(Peminjaman::class);
    }

    public function logAktivitas(): HasMany
    {
        return $this->hasMany(LogAktivitas::class);
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isPetugas(): bool
    {
        return $this->role === 'petugas';
    }

    public function isPeminjam(): bool
    {
        return $this->role === 'peminjam';
    }

    public function scopeSearch($query, ?string $keyword)
    {
        if ($keyword) {
            $query->where(function ($q) use ($keyword) {
                $q->where('name', 'like', "%{$keyword}%")
                    ->orWhere('email', 'like', "%{$keyword}%")
                    ->orWhere('no_hp', 'like', "%{$keyword}%");
            });
        }

        return $query;
    }

    public function scopeByRole($query, ?string $role)
    {
        if ($role) {
            $query->where('role', $role);
        }

        return $query;
    }

    public function getTierReputasiAttribute(): array
    {
        $skor = $this->skor_reputasi ?? 100;

        if ($skor >= 120) {
            return [
                'nama' => 'Peminjam Teladan',
                'badge' => 'badge-success',
                'color' => 'text-emerald-400',
                'icon' => '🌟',
                'deskripsi' => 'Reputasi istimewa. Pengembalian selalu tertib dan terpercaya.',
                'kuota_max' => 5,
            ];
        }

        if ($skor >= 90) {
            return [
                'nama' => 'Kredibel & Tertib',
                'badge' => 'badge-info',
                'color' => 'text-cyan-400',
                'icon' => '🟢',
                'deskripsi' => 'Reputasi baik. Peminjam disiplin.',
                'kuota_max' => 3,
            ];
        }

        if ($skor >= 60) {
            return [
                'nama' => 'Perlu Perhatian',
                'badge' => 'badge-warning',
                'color' => 'text-amber-400',
                'icon' => '🟡',
                'deskripsi' => 'Ada catatan keterlambatan pengembalian.',
                'kuota_max' => 2,
            ];
        }

        return [
            'nama' => 'Peninjauan / Rawan',
            'badge' => 'badge-danger',
            'color' => 'text-rose-400',
            'icon' => '🔴',
            'deskripsi' => 'Skor rendah. Sering terlambat atau merusak alat.',
            'kuota_max' => 1,
        ];
    }
}
