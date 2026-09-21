<?php

namespace Tests\Feature;

use App\Http\Controllers\AuthController;
use App\Models\Alat;
use App\Models\Kategori;
use App\Models\User;
use App\Services\PeminjamanService;
use App\Services\PengembalianService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PeminjamanFlowTest extends TestCase
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

    public function test_aplikasi_boot_tanpa_parse_error(): void
    {
        // Guard untuk bug #1: Controller.php unclosed brace.
        // Class abstrak tidak di-resolve langsung; gunakan salah satu turunannya.
        $this->assertTrue(class_exists(AuthController::class));
        $this->assertSame(
            'App\Http\Controllers\Controller',
            get_parent_class(AuthController::class)
        );
    }

    public function test_ajukan_menurunkan_stok_sekali(): void
    {
        $service = app(PeminjamanService::class);

        $result = $service->ajukan(
            $this->peminjam->id,
            '2026-09-21',
            '2026-09-25',
            [['alat_id' => $this->alat->id, 'jumlah' => 2]]
        );

        $this->assertTrue($result['success']);
        $this->assertSame(3, $this->alat->fresh()->stok, 'Stok harus turun 1x saat ajukan');
    }

    public function test_approve_tidak_menurunkan_stok_lagi(): void
    {
        // Guard untuk bug #2: double-decrement stok
        $service = app(PeminjamanService::class);

        $peminjaman = $service->ajukan(
            $this->peminjam->id,
            '2026-09-21',
            '2026-09-25',
            [['alat_id' => $this->alat->id, 'jumlah' => 2]]
        )['peminjaman'];

        $service->approve($peminjaman);

        $this->assertSame(3, $this->alat->fresh()->stok, 'Approve tidak boleh decrement stok lagi');
        $this->assertSame('dipinjam', $peminjaman->fresh()->status);
    }

    public function test_tolak_mengembalikan_stok(): void
    {
        $service = app(PeminjamanService::class);

        $peminjaman = $service->ajukan(
            $this->peminjam->id,
            '2026-09-21',
            '2026-09-25',
            [['alat_id' => $this->alat->id, 'jumlah' => 3]]
        )['peminjaman'];

        $service->tolak($peminjaman);

        $this->assertSame(5, $this->alat->fresh()->stok, 'Tolak harus mengembalikan stok');
        $this->assertDatabaseMissing('peminjaman', ['id' => $peminjaman->id]);
    }

    public function test_stok_tidak_bisa_negatif(): void
    {
        // Guard untuk bug #4: stok negatif
        $service = app(PeminjamanService::class);

        $result = $service->ajukan(
            $this->peminjam->id,
            '2026-09-21',
            '2026-09-25',
            [['alat_id' => $this->alat->id, 'jumlah' => 99]]
        );

        $this->assertFalse($result['success']);
        $this->assertSame(5, $this->alat->fresh()->stok);
    }

    public function test_pengembalian_menambah_stok_dan_hitung_denda(): void
    {
        $peminjamanService = app(PeminjamanService::class);
        $pengembalianService = app(PengembalianService::class);

        $peminjaman = $peminjamanService->ajukan(
            $this->peminjam->id,
            '2026-09-21',
            '2026-09-25',
            [['alat_id' => $this->alat->id, 'jumlah' => 2]]
        )['peminjaman'];

        $peminjamanService->approve($peminjaman);

        $result = $pengembalianService->proses(
            $peminjaman->fresh(),
            '2026-09-28',
            'baik',
            $this->petugas->id
        );

        $this->assertSame(3, (int) $result['terlambatHari']);
        $this->assertSame(3 * 5000, (int) $result['denda']);
        $this->assertSame(5, $this->alat->fresh()->stok, 'Stok kembali setelah pengembalian');
        $this->assertSame('telat', $peminjaman->fresh()->status);
    }

    public function test_pengembalian_sebelum_tenggat_tanpa_denda(): void
    {
        $peminjamanService = app(PeminjamanService::class);
        $pengembalianService = app(PengembalianService::class);

        $peminjaman = $peminjamanService->ajukan(
            $this->peminjam->id,
            '2026-09-21',
            '2026-09-25',
            [['alat_id' => $this->alat->id, 'jumlah' => 1]]
        )['peminjaman'];

        $peminjamanService->approve($peminjaman);

        $result = $pengembalianService->proses(
            $peminjaman->fresh(),
            '2026-09-23',
            'baik',
            $this->petugas->id
        );

        $this->assertSame(0, $result['denda']);
        $this->assertSame('dikembalikan', $peminjaman->fresh()->status);
    }

    public function test_kategori_deskripsi_tersimpan(): void
    {
        // Guard untuk bug #3: kolom kategori.deskripsi tidak ada di migrasi
        $kategori = Kategori::create([
            'nama_kategori' => 'Jaringan',
            'deskripsi' => 'Alat-alat jaringan komputer',
        ]);

        $this->assertSame('Alat-alat jaringan komputer', $kategori->fresh()->deskripsi);
    }

    public function test_login_petugas_bisa_akses_dashboard(): void
    {
        $this->actingAs($this->petugas)
            ->get(route('petugas.dashboard'))
            ->assertStatus(200);
    }

    public function test_peminjam_tidak_bisa_akses_admin(): void
    {
        $this->actingAs($this->peminjam)
            ->get(route('admin.dashboard'))
            ->assertStatus(403);
    }

    public function test_guest_diredirect_ke_login(): void
    {
        $this->get(route('admin.dashboard'))
            ->assertRedirect(route('login'));
    }
}
