<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ProfileTest extends TestCase
{
    use RefreshDatabase;

    private User $peminjam;

    protected function setUp(): void
    {
        parent::setUp();

        $this->peminjam = User::create([
            'name' => 'Peminjam Test',
            'email' => 'peminjam@test.com',
            'password' => 'password123',
            'role' => 'peminjam',
        ]);
    }

    public function test_tamu_tidak_bisa_akses_halaman_me(): void
    {
        $this->get('/me')->assertRedirect('/login');
    }

    public function test_user_login_bisa_lihat_biodata_sendiri(): void
    {
        $response = $this->actingAs($this->peminjam)->get('/me');

        $response->assertOk();
        $response->assertSee('Peminjam Test');
        $response->assertSee('peminjam@test.com');
        $response->assertSee('Ubah Biodata');
    }

    public function test_user_bisa_update_biodata_dan_foto(): void
    {
        Storage::fake('public');

        $response = $this->actingAs($this->peminjam)->put('/me', [
            'name' => 'Peminjam Update',
            'email' => 'peminjam@test.com',
            'no_hp' => '081234567890',
            'alamat' => 'Jl. Test No. 1',
            'foto_profile' => UploadedFile::fake()->image('foto.jpg'),
        ]);

        $response->assertRedirect(route('me'));

        $this->peminjam->refresh();
        $this->assertSame('Peminjam Update', $this->peminjam->name);
        $this->assertSame('081234567890', $this->peminjam->no_hp);
        $this->assertNotNull($this->peminjam->foto_profile);
        Storage::disk('public')->assertExists($this->peminjam->foto_profile);
    }

    public function test_update_tanpa_foto_tidak_menghapus_foto_lama(): void
    {
        Storage::fake('public');

        $this->peminjam->update([
            'foto_profile' => UploadedFile::fake()->image('lama.jpg')->store('foto_profile', 'public'),
        ]);
        $fotoLama = $this->peminjam->foto_profile;

        $this->actingAs($this->peminjam)->put('/me', [
            'name' => 'Peminjam Test',
            'email' => 'peminjam@test.com',
            'no_hp' => null,
            'alamat' => null,
        ])->assertRedirect(route('me'));

        $this->assertSame($fotoLama, $this->peminjam->refresh()->foto_profile);
        Storage::disk('public')->assertExists($fotoLama);
    }

    public function test_foto_lama_terhapus_saat_ganti_foto_baru(): void
    {
        Storage::fake('public');

        $this->peminjam->update([
            'foto_profile' => UploadedFile::fake()->image('lama.jpg')->store('foto_profile', 'public'),
        ]);
        $fotoLama = $this->peminjam->foto_profile;

        $this->actingAs($this->peminjam)->put('/me', [
            'name' => 'Peminjam Test',
            'email' => 'peminjam@test.com',
            'foto_profile' => UploadedFile::fake()->image('baru.jpg'),
        ])->assertRedirect(route('me'));

        Storage::disk('public')->assertMissing($fotoLama);
        $this->assertNotSame($fotoLama, $this->peminjam->refresh()->foto_profile);
    }

    public function test_accessor_url_null_saat_tidak_ada_foto(): void
    {
        $this->assertNull($this->peminjam->foto_profile_url);
    }

    public function test_accessor_url_null_saat_file_hilang(): void
    {
        Storage::fake('public');

        $this->peminjam->update(['foto_profile' => 'foto_profile/hilang.jpg']);

        $this->assertNull($this->peminjam->refresh()->foto_profile_url);
    }

    public function test_email_unik_diabaikan_untuk_diri_sendiri(): void
    {
        $this->actingAs($this->peminjam)->put('/me', [
            'name' => 'Peminjam Test',
            'email' => 'peminjam@test.com',
        ])->assertRedirect(route('me'));
    }
}
