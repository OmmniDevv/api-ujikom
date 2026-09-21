<?php

namespace App\Services;

use App\Models\Alat;
use App\Models\DetailPinjam;
use App\Models\Peminjaman;
use Illuminate\Support\Facades\DB;

class PeminjamanService
{
    public function ajukan(int $userId, string $tglPinjam, string $tglKembaliPlan, array $detail): array
    {
        DB::beginTransaction();
        try {
            $peminjaman = Peminjaman::create([
                'user_id' => $userId,
                'tgl_pinjam' => $tglPinjam,
                'tgl_kembali_plan' => $tglKembaliPlan,
                'status' => 'diajukan',
            ]);

            $namaAlat = [];

            // ponytail: race condition stok diabaikan — aplikasi single-server sekolah,
            // lockForUpdate + re-cek cukup. Kalau scale-out: pindah ke DB CHECK (stok >= 0).
            foreach ($detail as $item) {
                $alat = Alat::lockForUpdate()->findOrFail($item['alat_id']);

                if (! $alat->stokCukup($item['jumlah'])) {
                    DB::rollBack();

                    return [
                        'success' => false,
                        'error' => "Stok alat \"{$alat->nama_alat}\" tidak mencukupi (tersedia: {$alat->stok}).",
                        'peminjaman' => null,
                        'namaAlat' => [],
                    ];
                }

                $alat->decrement('stok', $item['jumlah']);

                DetailPinjam::create([
                    'peminjaman_id' => $peminjaman->id,
                    'alat_id' => $item['alat_id'],
                    'jumlah' => $item['jumlah'],
                ]);

                $namaAlat[] = "{$alat->nama_alat} ({$item['jumlah']} unit)";
            }

            DB::commit();

            return [
                'success' => true,
                'peminjaman' => $peminjaman,
                'namaAlat' => $namaAlat,
                'error' => null,
            ];

        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function approve(Peminjaman $peminjaman): void
    {
        if ($peminjaman->status !== 'diajukan') {
            throw new \RuntimeException('Hanya peminjaman berstatus "diajukan" yang bisa disetujui.');
        }

        // Stok sudah di-reserve saat pengajuan (lihat ajukan()).
        // Approve hanya mengubah status, tidak mengubah stok lagi.
        $peminjaman->update(['status' => 'dipinjam']);
    }

    public function tolak(Peminjaman $peminjaman): array
    {
        if ($peminjaman->status !== 'diajukan') {
            throw new \RuntimeException('Hanya peminjaman berstatus "diajukan" yang bisa ditolak.');
        }

        DB::beginTransaction();
        try {
            $namaAlat = [];

            foreach ($peminjaman->detailPinjam as $detail) {
                $detail->alat->increment('stok', $detail->jumlah);
                $namaAlat[] = $detail->alat->nama_alat;
            }

            $peminjaman->delete();
            DB::commit();

            return $namaAlat;

        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
