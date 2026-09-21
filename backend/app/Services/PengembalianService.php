<?php

namespace App\Services;

use App\Models\Peminjaman;
use App\Models\Pengembalian;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class PengembalianService
{
    public function proses(Peminjaman $peminjaman, string $tglKembali, string $kondisiKembali, int $petugasId): array
    {
        if ($peminjaman->status !== 'dipinjam') {
            throw new \RuntimeException('Peminjaman ini tidak dalam status "dipinjam".');
        }

        if ($peminjaman->pengembalian()->exists()) {
            throw new \RuntimeException('Pengembalian untuk peminjaman ini sudah diproses.');
        }

        DB::beginTransaction();
        try {
            $tglKembaliCarbon = Carbon::parse($tglKembali);
            $tglRencana = $peminjaman->tgl_kembali_plan;

            $terlambatHari = max(0, $tglKembaliCarbon->diffInDays($tglRencana, false) * -1);

            $denda = $terlambatHari * 5000;
            $namaAlat = [];

            foreach ($peminjaman->detailPinjam as $detail) {

                if ($kondisiKembali !== 'baik') {
                    $detail->alat->update(['status_kondisi' => $kondisiKembali]);
                }

                $detail->alat->increment('stok', $detail->jumlah);
                $namaAlat[] = "{$detail->alat->nama_alat} ({$detail->jumlah} unit)";
            }

            Pengembalian::create([
                'peminjaman_id' => $peminjaman->id,
                'tgl_kembali' => $tglKembali,
                'kondisi_kembali' => $kondisiKembali,
                'denda' => $denda,
                'petugas_id' => $petugasId,
            ]);

            $statusBaru = $terlambatHari > 0 ? 'telat' : 'dikembalikan';
            $peminjaman->update(['status' => $statusBaru]);

            DB::commit();

            return [
                'denda' => $denda,
                'terlambatHari' => $terlambatHari,
                'namaAlat' => $namaAlat,
                'statusBaru' => $statusBaru,
            ];

        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
