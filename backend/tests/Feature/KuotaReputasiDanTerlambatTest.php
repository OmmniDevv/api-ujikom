<?php

namespace Tests\Feature;

use App\Models\Alat;
use App\Models\DetailPinjam;
use App\Models\Kategori;
use App\Models\Peminjaman;
use App\Models\User;
use App\Services\PeminjamanService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class KuotaReputasiDanTerlambatTest extends TestCase
{
    use RefreshDatabase;

    private Kategori $kategori;

    protected function setUp(): void
    {
        parent::setUp();

        $this->kategori = Kategori::create([
            'nama_kategori' => 'Jaringan',
            'deskripsi' => 'Alat jaringan',
        ]);
    }

    private function buatPeminjam(int $skor): User
    {
        return User::create([
            'name' => 'Peminjam Skor '.$skor,
            'email' => 'skor'.$skor.'@test.com',
            'password' => 'password123',
            'role' => 'peminjam',
            'skor_reputasi' => $skor,
        ]);
    }

    private function buatAlat(string $nama): Alat
    {
        return Alat::create([
            'kategori_id' => $this->kategori->id,
            'nama_alat' => $nama,
            'stok' => 5,
            'status_kondisi' => 'baik',
        ]);
    }

    public function test_skor_rendah_ditolak_saat_melebihi_kuota(): void
    {
        $peminjam = $this->buatPeminjam(50);
        $alat1 = $this->buatAlat('Router A');
        $alat2 = $this->buatAlat('Router B');

        $result = app(PeminjamanService::class)->ajukan(
            $peminjam->id,
            now()->format('Y-m-d'),
            now()->addDays(3)->format('Y-m-d'),
            [
                ['alat_id' => $alat1->id, 'jumlah' => 1],
                ['alat_id' => $alat2->id, 'jumlah' => 1],
            ]
        );

        $this->assertFalse($result['success']);
        $this->assertStringContainsString('Kuota pinjam', $result['error']);
        $this->assertSame(5, $alat1->fresh()->stok);
        $this->assertDatabaseCount('peminjaman', 0);
    }

    public function test_alat_aktif_ikut_dihitung_dalam_kuota(): void
    {
        $peminjam = $this->buatPeminjam(50);
        $alat1 = $this->buatAlat('Router A');
        $alat2 = $this->buatAlat('Router B');
        $service = app(PeminjamanService::class);
        $tglPinjam = now()->format('Y-m-d');
        $tglKembali = now()->addDays(3)->format('Y-m-d');

        $pertama = $service->ajukan($peminjam->id, $tglPinjam, $tglKembali, [['alat_id' => $alat1->id, 'jumlah' => 1]]);
        $this->assertTrue($pertama['success']);

        $kedua = $service->ajukan($peminjam->id, $tglPinjam, $tglKembali, [['alat_id' => $alat2->id, 'jumlah' => 1]]);
        $this->assertFalse($kedua['success']);
        $this->assertSame(5, $alat2->fresh()->stok);
    }

    public function test_skor_tinggi_bisa_pinjam_banyak_alat(): void
    {
        $peminjam = $this->buatPeminjam(130);
        $detail = [];
        foreach (['Router A', 'Router B', 'Router C'] as $nama) {
            $detail[] = ['alat_id' => $this->buatAlat($nama)->id, 'jumlah' => 1];
        }

        $result = app(PeminjamanService::class)->ajukan(
            $peminjam->id,
            now()->format('Y-m-d'),
            now()->addDays(3)->format('Y-m-d'),
            $detail
        );

        $this->assertTrue($result['success']);
    }

    public function test_dashboard_petugas_menampilkan_peminjaman_terlambat(): void
    {
        $peminjam = $this->buatPeminjam(100);
        $petugas = User::create([
            'name' => 'Petugas Test',
            'email' => 'petugas@test.com',
            'password' => 'password123',
            'role' => 'petugas',
        ]);
        $alat = $this->buatAlat('Router Telat');

        $peminjaman = Peminjaman::create([
            'user_id' => $peminjam->id,
            'tgl_pinjam' => now()->subDays(6)->format('Y-m-d'),
            'tgl_kembali_plan' => now()->subDays(3)->format('Y-m-d'),
            'status' => 'dipinjam',
        ]);
        DetailPinjam::create([
            'peminjaman_id' => $peminjaman->id,
            'alat_id' => $alat->id,
            'jumlah' => 1,
        ]);

        $this->assertSame(3, $peminjaman->fresh()->hari_terlambat);

        $response = $this->actingAs($petugas)->get(route('petugas.dashboard'));
        $response->assertOk();
        $response->assertSee('Sudah Terlambat, Belum Kembali');
        $response->assertSee('Telat 3 hari');
        $response->assertSee('Rp 15.000');
    }

    public function test_dashboard_petugas_tidak_menampilkan_yang_belum_terlambat(): void
    {
        $peminjam = $this->buatPeminjam(100);
        $petugas = User::create([
            'name' => 'Petugas Test',
            'email' => 'petugas2@test.com',
            'password' => 'password123',
            'role' => 'petugas',
        ]);
        $alat = $this->buatAlat('Router Tepat');

        $peminjaman = Peminjaman::create([
            'user_id' => $peminjam->id,
            'tgl_pinjam' => now()->format('Y-m-d'),
            'tgl_kembali_plan' => now()->addDays(2)->format('Y-m-d'),
            'status' => 'dipinjam',
        ]);
        DetailPinjam::create([
            'peminjaman_id' => $peminjaman->id,
            'alat_id' => $alat->id,
            'jumlah' => 1,
        ]);

        $response = $this->actingAs($petugas)->get(route('petugas.dashboard'));
        $response->assertOk();
        $response->assertDontSee('Sudah Terlambat, Belum Kembali');
    }
}
