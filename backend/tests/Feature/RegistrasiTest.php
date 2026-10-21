<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class RegistrasiTest extends TestCase
{
    use RefreshDatabase;

    private function payload(array $overrides = []): array
    {
        return array_merge([
            'name' => 'Budi Santoso',
            'email' => 'budi@siswa.sch.id',
            'password' => 'rahasia123',
            'password_confirmation' => 'rahasia123',
            'no_hp' => '081234567890',
            'alamat' => 'Jl. Merdeka No. 1, Baleendah',
        ], $overrides);
    }

    public function test_halaman_register_bisa_dibuka_tamu(): void
    {
        $this->get(route('register'))->assertOk();
    }

    public function test_register_berhasil_membuat_user_peminjam_dan_login(): void
    {
        $response = $this->post(route('register.post'), $this->payload());

        $this->assertDatabaseHas('users', [
            'email' => 'budi@siswa.sch.id',
            'name' => 'Budi Santoso',
            'role' => 'peminjam',
            'no_hp' => '081234567890',
            'alamat' => 'Jl. Merdeka No. 1, Baleendah',
        ]);

        $this->assertAuthenticated();

        $user = User::where('email', 'budi@siswa.sch.id')->first();
        $this->assertTrue(Hash::check('rahasia123', $user->password));

        $response->assertRedirect(route('peminjam.dashboard'));
    }

    public function test_register_gagal_email_duplikat(): void
    {
        User::factory()->create(['email' => 'budi@siswa.sch.id']);

        $this->post(route('register.post'), $this->payload())
            ->assertSessionHasErrors('email');

        $this->assertGuest();
    }

    public function test_register_gagal_konfirmasi_password_tidak_cocok(): void
    {
        $this->post(
            route('register.post'),
            $this->payload(['password_confirmation' => 'salah123'])
        )->assertSessionHasErrors('password');

        $this->assertGuest();
    }

    public function test_register_gagal_password_5_karakter_ditolak(): void
    {
        $this->post(
            route('register.post'),
            $this->payload(['password' => 'abcde', 'password_confirmation' => 'abcde'])
        )->assertSessionHasErrors('password');

        $this->assertGuest();
        $this->assertDatabaseMissing('users', ['email' => 'budi@siswa.sch.id']);
    }

    public function test_register_berhasil_password_6_karakter_diterima(): void
    {
        $response = $this->post(
            route('register.post'),
            $this->payload(['password' => 'abcdef', 'password_confirmation' => 'abcdef'])
        );

        $this->assertAuthenticated();

        $user = User::where('email', 'budi@siswa.sch.id')->first();
        $this->assertNotNull($user);
        $this->assertTrue(Hash::check('abcdef', $user->password));

        $response->assertRedirect(route('peminjam.dashboard'));
    }

    public function test_register_tidak_bisa_menaikkan_role_sendiri(): void
    {
        $this->post(route('register.post'), $this->payload(['role' => 'admin']))
            ->assertRedirect(route('peminjam.dashboard'));

        $this->assertDatabaseHas('users', [
            'email' => 'budi@siswa.sch.id',
            'role' => 'peminjam',
        ]);
    }

    public function test_user_sudah_login_diarahkan_ke_beranda(): void
    {
        $user = User::factory()->create(['role' => 'peminjam']);

        // Middleware guest me-redirect user yang sudah login ke '/'
        // (route '/' sendiri yang kemudian mengarahkan per role)
        $this->actingAs($user)->get(route('register'))
            ->assertRedirect('/');
    }

    public function test_register_mencatat_log_aktivitas(): void
    {
        $this->post(route('register.post'), $this->payload());

        $this->assertDatabaseHas('log_aktivitas', ['aktivitas' => 'Registrasi']);
    }
}
