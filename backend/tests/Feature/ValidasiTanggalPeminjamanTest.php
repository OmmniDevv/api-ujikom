<?php

namespace Tests\Feature;

use App\Models\Alat;
use App\Models\Kategori;
use App\Models\Peminjaman;
use App\Models\User;
use App\Services\PeminjamanService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ValidasiTanggalPeminjamanTest extends TestCase
{
    use RefreshDatabase;

    private User $peminjam;

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

        $this->alat = Alat::create([
            'kategori_id' => $kategori->id,
            'nama_alat' => 'Router Test',
            'stok' => 5,
            'status_kondisi' => 'baik',
        ]);
    }

    public function test_kembali_sebelum_pinjam_ditolak(): void
    {
        // Guard konsistensi dengan StorePeminjamanRequest: tgl_kembali_plan
        // sebelum tgl_pinjam harus ditolak di level service juga.
        $service = app(PeminjamanService::class);

        $result = $service->ajukan(
            $this->peminjam->id,
            '2026-09-21',
            '2026-09-20',
            [['alat_id' => $this->alat->id, 'jumlah' => 2]]
        );

        $this->assertFalse($result['success']);
        $this->assertSame('Tanggal kembali tidak boleh sebelum tanggal pinjam.', $result['error']);
        $this->assertNull($result['peminjaman']);

        // Tidak ada record peminjaman/detail yang tersimpan.
        $this->assertSame(0, Peminjaman::count(), 'Tidak boleh ada record peminjaman yang tersimpan');
        $this->assertDatabaseCount('detail_pinjam', 0);

        // Stok alat tidak boleh berubah.
        $this->assertSame(5, $this->alat->fresh()->stok, 'Stok tidak boleh berubah saat ajukan ditolak');
    }

    public function test_kembali_di_hari_yang_sama_diperbolehkan(): void
    {
        // atur: after_or_equal:tgl_pinjam memperbolehkan hari yang sama.
        $service = app(PeminjamanService::class);

        $result = $service->ajukan(
            $this->peminjam->id,
            '2026-09-21',
            '2026-09-21',
            [['alat_id' => $this->alat->id, 'jumlah' => 1]]
        );

        $this->assertTrue($result['success']);
        $this->assertNotNull($result['peminjaman']);
        $this->assertSame(4, $this->alat->fresh()->stok, 'Stok turun normal saat tanggal kembali = tanggal pinjam');
    }
}
