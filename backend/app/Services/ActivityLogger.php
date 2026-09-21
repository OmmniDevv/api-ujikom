<?php

namespace App\Services;

use App\Models\LogAktivitas;
use Illuminate\Support\Facades\Log;

class ActivityLogger
{
    public static function log(string $aksi, ?string $keterangan = null, ?int $userId = null): void
    {
        $userId = $userId ?? auth()->id();

        try {
            LogAktivitas::create([
                'user_id' => $userId,
                'aktivitas' => $aksi,
                'keterangan' => $keterangan,
            ]);
        } catch (\Exception $e) {
            Log::error('Gagal simpan log aktivitas ke DB: '.$e->getMessage());
        }

        Log::info("[AKTIVITAS] {$aksi}", [
            'user_id' => $userId,
            'keterangan' => $keterangan,
            'timestamp' => now()->toDateTimeString(),
        ]);
    }

    public static function logError(string $aksi, string $error, ?int $userId = null): void
    {
        Log::warning("[AKTIVITAS-GAGAL] {$aksi}", [
            'user_id' => $userId ?? auth()->id(),
            'error' => $error,
            'timestamp' => now()->toDateTimeString(),
        ]);
    }
}
