<?php

namespace Tests\Feature;

use App\Models\Alat;
use App\Models\Kategori;
use App\Models\Pengembalian;
use App\Models\User;
use App\Services\PeminjamanService;
use App\Services\PengembalianService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DendaManualTest extends TestCase
{
    use RefreshDatabase;

    private User $peminjam;

    private User $petugas;

    private Alat $alat;

    protected function setUp(): void
    {
        parent::setUp();

        $kategori = Kategori::create([
            'nama_kategori' => 'Jaringan',
            'deskripsi' => 'Alat jaringan',
        ]);

        $this->peminjam = User::create([
            'name' => 'Peminjam Test',
            'email' => 'peminjam@test.com',
            'password' => 'password123',
            'role' => 'peminjam',
        ]);

        $this->petugas = User::create([
            'name' => 'Petugas Test',
            'email' => 'petugas@test.com',
            'password' => 'password123',
            'role' => 'petugas',
        ]);

        $this->alat = Alat::create([
            'kategori_id' => $kategori->id,
            'nama_alat' => 'Router Test',
            'stok' => 5,
            'status_kondisi' => 'baik',
        ]);
    }

    private function pinjamDanSetujui(string $tglKembaliPlan): \App\Models\Peminjaman
    {
        $service = app(PeminjamanService::class);

        $peminjaman = $service->ajukan(
            $this->peminjam->id,
            '2026-09-21',
            $tglKembaliPlan,
            [['alat_id' => $this->alat->id, 'jumlah' => 2]]
        )['peminjaman'];

        $service->approve($peminjaman);

        return $peminjaman->fresh();
    }

    public function test_denda_manual_mengoverride_hitung_otomatis_untuk_alat_rusak(): void
    {
        // Skenario kbli-pos-003: kembali TEPAT WAKTU dengan kondisi 'rusak'
        // dan denda manual 200000. Tanpa denda manual, dendanya akan 0.
        $peminjaman = $this->pinjamDanSetujui('2026-09-25');

        $service = app(PengembalianService::class);

        $result = $service->proses(
            $peminjaman,
            '2026-09-25', // tepat pada tenggat, tidak terlambat
            'rusak',
            $this->petugas->id,
            'Router pecah saat dipakai praktikum',
            200000 // denda manual
        );

        $this->assertSame(0, $result['terlambatHari']);
        $this->assertSame(200000, $result['denda'], 'Denda manual harus meng-override hitungan otomatis (yang bernilai 0)');
        $this->assertDatabaseHas('pengembalian', [
            'peminjaman_id' => $peminjaman->id,
            'denda' => 200000,
        ]);
        $this->assertSame(200000, (int) Pengembalian::where('peminjaman_id', $peminjaman->id)->value('denda'));
        $this->assertSame('rusak', $this->alat->fresh()->status_kondisi);
    }

    public function test_denda_manual_nol_menghapus_denda_telat(): void
    {
        // Skenario: kembali TERLAMBAT 3 hari dengan denda manual 0 eksplisit.
        // Tanpa denda manual, denda otomatisnya 3 x 5000 = 15000.
        $peminjaman = $this->pinjamDanSetujui('2026-09-25');

        $service = app(PengembalianService::class);

        $result = $service->proses(
            $peminjaman,
            '2026-09-28', // terlambat 3 hari
            'baik',
            $this->petugas->id,
            null,
            0 // denda manual 0 eksplisit
        );

        $this->assertSame(3, $result['terlambatHari']);
        $this->assertSame(0, $result['denda'], 'Denda manual 0 eksplisit harus meng-override hitungan otomatis 15000, bukan dianggap kosong/null');
        $this->assertDatabaseHas('pengembalian', [
            'peminjaman_id' => $peminjaman->id,
            'denda' => 0,
        ]);
    }
}
