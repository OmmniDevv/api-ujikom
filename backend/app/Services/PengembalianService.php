<?php

namespace App\Services;

use App\Models\Peminjaman;
use App\Models\Pengembalian;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class PengembalianService
{
    public function proses(Peminjaman $peminjaman, string $tglKembali, string $kondisiKembali, int $petugasId, ?string $catatan = null): array
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

            $terlambatHari = max(0, (int) $tglRencana->startOfDay()->diffInDays($tglKembaliCarbon->startOfDay(), false));

            $denda = $terlambatHari * 5000;
            $namaAlat = [];

            foreach ($peminjaman->detailPinjam as $detail) {
                // Guard: alat mungkin sudah dihapus (soft issue) — lewati agar tidak NPE
                if (! $detail->alat) {
                    continue;
                }
                if ($kondisiKembali !== 'baik') {
                    $detail->alat->update(['status_kondisi' => $kondisiKembali]);
                }
                $detail->alat->increment('stok', $detail->jumlah);
                $namaAlat[] = "{$detail->alat->nama_alat} ({$detail->jumlah} unit)";
            }

            $payload = [
                'peminjaman_id' => $peminjaman->id,
                'tgl_kembali' => $tglKembali,
                'kondisi_kembali' => $kondisiKembali,
                'denda' => $denda,
                'petugas_id' => $petugasId,
            ];
            // Simpan catatan jika kolom tersedia (migrasi akan menambahkannya); aman untuk backward-compat
            if ($catatan !== null && \Illuminate\Support\Facades\Schema::hasColumn('pengembalian', 'catatan')) {
                $payload['catatan'] = $catatan;
            }
            Pengembalian::create($payload);

            $statusBaru = $terlambatHari > 0 ? 'telat' : 'dikembalikan';
            $peminjaman->update(['status' => $statusBaru]);

            // Pembaruan skor reputasi & gamifikasi peminjam
            $userPeminjam = $peminjaman->user;
            if ($userPeminjam) {
                $skorSekarang = $userPeminjam->skor_reputasi ?? 100;
                $deltaSkor = 0;
                if ($terlambatHari === 0) {
                    $deltaSkor += 2; // Reward tepat waktu
                } else {
                    $deltaSkor -= min(50, $terlambatHari * 5); // Penalti telat
                }

                if ($kondisiKembali === 'rusak') {
                    $deltaSkor -= 20; // Penalti alat rusak
                } elseif ($kondisiKembali === 'perbaikan') {
                    $deltaSkor -= 10;
                }

                $skorBaru = max(0, min(150, $skorSekarang + $deltaSkor));
                $userPeminjam->update(['skor_reputasi' => $skorBaru]);
            }

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
