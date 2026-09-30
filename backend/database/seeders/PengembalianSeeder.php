<?php

namespace Database\Seeders;

use App\Models\Pengembalian;
use Illuminate\Database\Seeder;

class PengembalianSeeder extends Seeder
{
    public function run(): void
    {
        $pengembalian = [
            [
                'peminjaman_id' => 1,
                'tgl_kembali' => '2026-06-04',
                'kondisi_kembali' => 'baik',
                'denda' => 0,
                'petugas_id' => 2, // Arif (Petugas)
            ],
            [
                'peminjaman_id' => 2,
                'tgl_kembali' => '2026-06-05',
                'kondisi_kembali' => 'baik',
                'denda' => 0,
                'petugas_id' => 2,
            ],
            [
                'peminjaman_id' => 3,
                'tgl_kembali' => '2026-06-09', // Telat 3 hari dari tgl 6 (3 x Rp 5.000 = Rp 15.000)
                'kondisi_kembali' => 'rusak',
                'denda' => 15000,
                'petugas_id' => 2,
            ],
        ];

        foreach ($pengembalian as $kembali) {
            Pengembalian::create($kembali);
        }
    }
}
