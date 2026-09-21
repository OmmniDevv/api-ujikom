<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Kategori extends Model
{
    protected $table = 'kategori';

    protected $fillable = ['nama_kategori', 'deskripsi'];

    public function alat(): HasMany
    {
        return $this->hasMany(Alat::class);
    }

    public function masihDigunakan(): bool
    {
        return $this->alat()->exists();
    }

    public function scopeSearch($query, ?string $keyword)
    {
        if ($keyword) {
            $query->where('nama_kategori', 'like', "%{$keyword}%")
                ->orWhere('deskripsi', 'like', "%{$keyword}%");
        }

        return $query;
    }
}
