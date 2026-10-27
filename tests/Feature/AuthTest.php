<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_page_can_be_rendered(): void
    {
        $response = $this->get('/login');

        $response->assertStatus(200);
        $response->assertSee('Masuk ke Sistem');
        $response->assertSee('KOMPLAIN');
        $response->assertSee('ilustrasi-login.png');
    }

    public function test_login_page_renders_with_default_role_query(): void
    {
        $response = $this->get('/login?role=admin');

        $response->assertStatus(200);
        $response->assertViewHas('defaultRole', 'admin');
    }

    public function test_pegawai_can_login_with_email_and_redirects_to_pegawai_dashboard(): void
    {
        $user = User::factory()->create([
            'email' => 'pegawai@komplain.test',
            'password' => Hash::make('secret123'),
            'role' => 'pegawai',
            'is_aktif' => true,
        ]);

        $response = $this->post('/login', [
            'login' => 'pegawai@komplain.test',
            'password' => 'secret123',
        ]);

        $response->assertRedirect(route('pegawai.dashboard'));
        $this->assertAuthenticatedAs($user);
    }

    public function test_pegawai_can_login_with_nip(): void
    {
        $user = User::factory()->create([
            'nip' => '199001012015011002',
            'password' => Hash::make('secret123'),
            'role' => 'pegawai',
            'is_aktif' => true,
        ]);

        $response = $this->post('/login', [
            'login' => '199001012015011002',
            'password' => 'secret123',
        ]);

        $response->assertRedirect(route('pegawai.dashboard'));
        $this->assertAuthenticatedAs($user);
    }

    public function test_admin_can_login_and_redirects_to_admin_dashboard(): void
    {
        $admin = User::factory()->create([
            'email' => 'admin@komplain.test',
            'password' => Hash::make('secret123'),
            'role' => 'admin',
            'is_aktif' => true,
        ]);

        $response = $this->post('/login', [
            'login' => 'admin@komplain.test',
            'password' => 'secret123',
        ]);

        $response->assertRedirect(route('admin.dashboard'));
        $this->assertAuthenticatedAs($admin);
    }

    public function test_both_pegawai_and_admin_can_login_through_unified_form(): void
    {
        $pegawai = User::factory()->create([
            'email' => 'pengadu@komplain.test',
            'password' => Hash::make('secret123'),
            'role' => 'pegawai',
            'is_aktif' => true,
        ]);

        $responsePegawai = $this->post('/login', [
            'login' => 'pengadu@komplain.test',
            'password' => 'secret123',
        ]);

        $responsePegawai->assertRedirect(route('pegawai.dashboard'));
        $this->assertAuthenticatedAs($pegawai);

        auth()->logout();

        $admin = User::factory()->create([
            'email' => 'admin_baru@komplain.test',
            'password' => Hash::make('secret123'),
            'role' => 'admin',
            'is_aktif' => true,
        ]);

        $responseAdmin = $this->post('/login', [
            'login' => 'admin_baru@komplain.test',
            'password' => 'secret123',
        ]);

        $responseAdmin->assertRedirect(route('admin.dashboard'));
        $this->assertAuthenticatedAs($admin);
    }

    public function test_inactive_user_cannot_login(): void
    {
        User::factory()->create([
            'email' => 'inactive@komplain.test',
            'password' => Hash::make('secret123'),
            'role' => 'pegawai',
            'is_aktif' => false,
        ]);

        $response = $this->post('/login', [
            'login' => 'inactive@komplain.test',
            'password' => 'secret123',
        ]);

        $response->assertSessionHasErrors('login');
        $this->assertGuest();
    }

    public function test_user_cannot_login_with_invalid_password(): void
    {
        User::factory()->create([
            'email' => 'user@komplain.test',
            'password' => Hash::make('secret123'),
            'role' => 'pegawai',
            'is_aktif' => true,
        ]);

        $response = $this->post('/login', [
            'login' => 'user@komplain.test',
            'password' => 'wrong-password',
        ]);

        $response->assertSessionHasErrors('password');
        $this->assertGuest();
    }

    public function test_authenticated_admin_visiting_login_redirects_to_admin_dashboard(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'is_aktif' => true]);

        $response = $this->actingAs($admin)->get('/login');

        $response->assertRedirect(route('admin.dashboard'));
    }

    public function test_authenticated_pengadu_visiting_login_redirects_to_pegawai_dashboard(): void
    {
        $pengadu = User::factory()->create(['role' => 'pegawai', 'is_aktif' => true]);

        $response = $this->actingAs($pengadu)->get('/login');

        $response->assertRedirect(route('pegawai.dashboard'));
    }

    public function test_user_can_logout(): void
    {
        $user = User::factory()->create(['role' => 'pegawai', 'is_aktif' => true]);

        $response = $this->actingAs($user)->post('/logout');

        $response->assertRedirect(route('home'));
        $this->assertGuest();
    }
}
