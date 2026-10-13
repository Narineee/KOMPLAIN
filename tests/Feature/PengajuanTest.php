<?php

namespace Tests\Feature;

use App\Models\Kategori;
use App\Models\Pengajuan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PengajuanTest extends TestCase
{
    use RefreshDatabase;

    public function test_pegawai_can_view_create_pengajuan_form(): void
    {
        $pegawai = User::factory()->create(['role' => 'pegawai', 'is_aktif' => true]);

        $response = $this->actingAs($pegawai)->get(route('pegawai.pengajuan.create'));

        $response->assertStatus(200);
    }

    public function test_guest_cannot_access_create_pengajuan(): void
    {
        $response = $this->get(route('pegawai.pengajuan.create'));

        $response->assertRedirect('/login');
    }

    public function test_pegawai_can_create_pengajuan_with_identity(): void
    {
        $pegawai = User::factory()->create(['role' => 'pegawai', 'is_aktif' => true]);
        $kategori = Kategori::create(['nama' => 'Fasilitas Kantor', 'is_aktif' => true]);

        $response = $this->actingAs($pegawai)->post(route('pegawai.pengajuan.store'), [
            'kategori_id' => $kategori->id,
            'judul' => 'Kerusakan AC di Ruang Rapat',
            'isi_laporan' => 'AC di ruang rapat lantai 2 mengeluarkan air dan tidak dingin.',
            'is_anonim' => '0',
        ]);

        $response->assertRedirect(route('pegawai.pengajuan.sukses'));
        $response->assertSessionHas('tiket_sukses');

        $this->assertDatabaseHas('pengajuans', [
            'user_id' => $pegawai->id,
            'kategori_id' => $kategori->id,
            'judul' => 'Kerusakan AC di Ruang Rapat',
            'is_anonim' => false,
            'status' => 'diajukan',
        ]);

        $pengajuan = Pengajuan::first();
        $this->assertNotNull($pengajuan->tokenPengajuan);
        $this->assertNotEmpty($pengajuan->tokenPengajuan->token_hash);
    }

    public function test_pegawai_can_create_pengajuan_anonymously_and_user_id_is_null(): void
    {
        $pegawai = User::factory()->create(['role' => 'pegawai', 'is_aktif' => true]);
        $kategori = Kategori::create(['nama' => 'Pelayanan', 'is_aktif' => true]);

        $response = $this->actingAs($pegawai)->post(route('pegawai.pengajuan.store'), [
            'kategori_id' => $kategori->id,
            'judul' => 'Laporan Anonim Terkait SOP',
            'isi_laporan' => 'Terdapat hambatan birokrasi dalam persetujuan dokumen.',
            'is_anonim' => '1',
        ]);

        $response->assertRedirect(route('pegawai.pengajuan.sukses'));

        $this->assertDatabaseHas('pengajuans', [
            'user_id' => null,
            'kategori_id' => $kategori->id,
            'is_anonim' => true,
        ]);
    }

    public function test_pegawai_can_view_own_non_anonim_pengajuan(): void
    {
        $pegawai = User::factory()->create(['role' => 'pegawai', 'is_aktif' => true]);
        $kategori = Kategori::create(['nama' => 'Pelayanan', 'is_aktif' => true]);

        $pengajuan = Pengajuan::create([
            'nomor_tiket' => 'TKT-20261009-ABCDE',
            'user_id' => $pegawai->id,
            'kategori_id' => $kategori->id,
            'judul' => 'Laporan Saya',
            'isi_laporan' => 'Rincian laporan milik saya.',
            'is_anonim' => false,
            'status' => 'diajukan',
        ]);

        $response = $this->actingAs($pegawai)->get(route('pegawai.pengajuan.show', $pengajuan));

        $response->assertStatus(200);
    }

    public function test_pegawai_cannot_view_other_users_pengajuan(): void
    {
        $user1 = User::factory()->create(['role' => 'pegawai', 'is_aktif' => true]);
        $user2 = User::factory()->create(['role' => 'pegawai', 'is_aktif' => true]);
        $kategori = Kategori::create(['nama' => 'Pelayanan', 'is_aktif' => true]);

        $pengajuan = Pengajuan::create([
            'nomor_tiket' => 'TKT-20261009-ABCDE',
            'user_id' => $user1->id,
            'kategori_id' => $kategori->id,
            'judul' => 'Laporan User 1',
            'isi_laporan' => 'Rincian laporan user 1.',
            'is_anonim' => false,
            'status' => 'diajukan',
        ]);

        $response = $this->actingAs($user2)->get(route('pegawai.pengajuan.show', $pengajuan));

        $response->assertStatus(403);
    }
}
