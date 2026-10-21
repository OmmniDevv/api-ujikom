<?php

namespace Tests\Feature;

use App\Models\Alat;
use App\Models\Kategori;
use App\Models\User;
use App\Services\PeminjamanService;
use App\Services\PengembalianService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Test cakupan logika skor reputasi user di PengembalianService::proses().
 *
 * Aturan yang diuji (App\Services\PengembalianService):
 * - tepat waktu (terlambatHari === 0) : +2
 * - telat                              : -5 per hari, maksimal -50
 * - kondisi 'rusak'                    : -20
 * - kondisi 'perbaikan'                : -10
 * - skor baru dibatasi rentang 0..150
 * - skor awal kolom skor_reputasi default 100
 */
class SkorReputasiTest extends TestCase
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

    /** Buat peminjaman baru dalam status 'dipinjam' (tenggat 2026-09-25). */
    private function buatPeminjamanDipinjam(?User $peminjam = null)
    {
        $service = app(PeminjamanService::class);

        $peminjaman = $service->ajukan(
            ($peminjam ?? $this->peminjam)->id,
            '2026-09-21',
            '2026-09-25',
            [['alat_id' => $this->alat->id, 'jumlah' => 1]]
        )['peminjaman'];

        $service->approve($peminjaman);

        return $peminjaman;
    }

    private function prosesKembali($peminjaman, string $tglKembali, string $kondisi): void
    {
        app(PengembalianService::class)->proses(
            $peminjaman->fresh(),
            $tglKembali,
            $kondisi,
            $this->petugas->id
        );
    }

    public function test_skor_awal_default_100(): void
    {
        $this->assertSame(100, (int) $this->peminjam->fresh()->skor_reputasi);
    }

    public function test_tepat_waktu_tambah_2(): void
    {
        $peminjaman = $this->buatPeminjamanDipinjam();
        $this->prosesKembali($peminjaman, '2026-09-25', 'baik');

        $this->assertSame(102, (int) $this->peminjam->fresh()->skor_reputasi);
    }

    public function test_tepat_waktu_sebelum_tenggat_tambah_2(): void
    {
        $peminjaman = $this->buatPeminjamanDipinjam();
        $this->prosesKembali($peminjaman, '2026-09-23', 'baik');

        $this->assertSame(102, (int) $this->peminjam->fresh()->skor_reputasi);
    }

    public function test_telat_kurang_5_per_hari(): void
    {
        $peminjaman = $this->buatPeminjamanDipinjam();
        $this->prosesKembali($peminjaman, '2026-09-28', 'baik'); // 3 hari telat

        $this->assertSame(85, (int) $this->peminjam->fresh()->skor_reputasi);
    }

    public function test_telat_tidak_dapat_reward_tepat_waktu(): void
    {
        $peminjaman = $this->buatPeminjamanDipinjam();
        $this->prosesKembali($peminjaman, '2026-09-26', 'baik'); // 1 hari telat

        // Bukan 100 + 2 - 5 = 97, melainkan 100 - 5 = 95.
        $this->assertSame(95, (int) $this->peminjam->fresh()->skor_reputasi);
    }

    public function test_penalti_telat_dibatasi_maks_50(): void
    {
        $peminjaman = $this->buatPeminjamanDipinjam();
        $this->prosesKembali($peminjaman, '2026-10-25', 'baik'); // 30 hari telat

        $this->assertSame(50, (int) $this->peminjam->fresh()->skor_reputasi);
    }

    public function test_kondisi_rusak_kurang_20(): void
    {
        $peminjaman = $this->buatPeminjamanDipinjam();
        $this->prosesKembali($peminjaman, '2026-09-25', 'rusak');

        $this->assertSame(82, (int) $this->peminjam->fresh()->skor_reputasi);
    }

    public function test_kondisi_perbaikan_kurang_10(): void
    {
        $peminjaman = $this->buatPeminjamanDipinjam();
        $this->prosesKembali($peminjaman, '2026-09-25', 'perbaikan');

        $this->assertSame(92, (int) $this->peminjam->fresh()->skor_reputasi);
    }

    public function test_telat_dan_rusak_akumulatif(): void
    {
        $peminjaman = $this->buatPeminjamanDipinjam();
        $this->prosesKembali($peminjaman, '2026-09-27', 'rusak'); // 2 hari telat

        // 100 - (2*5) - 20 = 70
        $this->assertSame(70, (int) $this->peminjam->fresh()->skor_reputasi);
    }

    public function test_skor_dibatasi_bawah_nol(): void
    {
        $this->peminjam->update(['skor_reputasi' => 5]);

        $peminjaman = $this->buatPeminjamanDipinjam();
        $this->prosesKembali($peminjaman, '2026-09-27', 'rusak'); // 5 - 10 - 20 = -25

        $this->assertSame(0, (int) $this->peminjam->fresh()->skor_reputasi);
    }

    public function test_skor_dibatasi_atas_150(): void
    {
        $this->peminjam->update(['skor_reputasi' => 149]);

        $peminjaman = $this->buatPeminjamanDipinjam();
        $this->prosesKembali($peminjaman, '2026-09-25', 'baik'); // 149 + 2 = 151 -> 150

        $this->assertSame(150, (int) $this->peminjam->fresh()->skor_reputasi);
    }

    public function test_delta_dihitung_dari_skor_terakhir_bukan_selalu_default(): void
    {
        $this->peminjam->update(['skor_reputasi' => 120]);

        $peminjaman = $this->buatPeminjamanDipinjam();
        $this->prosesKembali($peminjaman, '2026-09-25', 'baik');

        $this->assertSame(122, (int) $this->peminjam->fresh()->skor_reputasi);
    }
}
