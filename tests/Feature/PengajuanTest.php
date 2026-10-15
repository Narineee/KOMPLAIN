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
        $kategori = Kategori::create(['nama_kategori' => 'Fasilitas Kantor', 'aktif' => true]);

        $response = $this->actingAs($pegawai)->post(route('pegawai.pengajuan.store'), [
            'kategori_id' => $kategori->id,
            'isi_laporan' => 'AC di ruang rapat lantai 2 mengeluarkan air dan tidak dingin.',
            'is_anonim' => '0',
        ]);

        $response->assertRedirect(route('pegawai.dashboard'));
        $response->assertSessionHas('status');

        $this->assertDatabaseHas('pengajuan', [
            'user_id' => $pegawai->id,
            'kategori_id' => $kategori->id,
            'deskripsi' => 'AC di ruang rapat lantai 2 mengeluarkan air dan tidak dingin.',
            'metode_identitas' => 'identitas',
            'status' => 'menunggu',
        ]);
    }

    public function test_pegawai_can_create_pengajuan_anonymously_and_user_id_is_null(): void
    {
        $pegawai = User::factory()->create(['role' => 'pegawai', 'is_aktif' => true]);
        $kategori = Kategori::create(['nama_kategori' => 'Pelayanan', 'aktif' => true]);

        $response = $this->actingAs($pegawai)->post(route('pegawai.pengajuan.store'), [
            'kategori_id' => $kategori->id,
            'isi_laporan' => 'Terdapat hambatan birokrasi dalam persetujuan dokumen.',
            'is_anonim' => '1',
        ]);

        $response->assertRedirect(route('pegawai.dashboard'));

        $this->assertDatabaseHas('pengajuan', [
            'user_id' => $pegawai->id,
            'kategori_id' => $kategori->id,
            'metode_identitas' => 'anonim',
        ]);
    }

    public function test_pegawai_can_view_own_non_anonim_pengajuan(): void
    {
        $pegawai = User::factory()->create(['role' => 'pegawai', 'is_aktif' => true]);
        $kategori = Kategori::create(['nama_kategori' => 'Pelayanan', 'aktif' => true]);

        $pengajuan = Pengajuan::create([
            'user_id' => $pegawai->id,
            'kategori_id' => $kategori->id,
            'deskripsi' => 'Rincian laporan milik saya.',
            'metode_identitas' => 'identitas',
            'status' => 'menunggu',
        ]);

        $response = $this->actingAs($pegawai)->get(route('pegawai.pengajuan.show', $pengajuan));

        $response->assertStatus(200);
    }

    public function test_pegawai_cannot_view_other_users_pengajuan(): void
    {
        $user1 = User::factory()->create(['role' => 'pegawai', 'is_aktif' => true]);
        $user2 = User::factory()->create(['role' => 'pegawai', 'is_aktif' => true]);
        $kategori = Kategori::create(['nama_kategori' => 'Pelayanan', 'aktif' => true]);

        $pengajuan = Pengajuan::create([
            'user_id' => $user1->id,
            'kategori_id' => $kategori->id,
            'deskripsi' => 'Rincian laporan user 1.',
            'metode_identitas' => 'identitas',
            'status' => 'menunggu',
        ]);

        $response = $this->actingAs($user2)->get(route('pegawai.pengajuan.show', $pengajuan));

        $response->assertStatus(403);
    }

    public function test_pegawai_sees_progress_stepper_and_sub_processes(): void
    {
        $pegawai = User::factory()->create(['role' => 'pegawai', 'is_aktif' => true]);
        $kategori = Kategori::create(['nama_kategori' => 'Fasilitas', 'aktif' => true]);

        // 1. Pengajuan pada tahap Diproses - Klarifikasi
        $pengajuanProses = Pengajuan::create([
            'user_id' => $pegawai->id,
            'kategori_id' => $kategori->id,
            'deskripsi' => 'Pendingin ruangan tidak berfungsi di lobi.',
            'metode_identitas' => 'identitas',
            'status' => 'diproses',
            'sub_status' => 'klarifikasi',
        ]);

        $responseProses = $this->actingAs($pegawai)->get(route('pegawai.pengajuan.show', $pengajuanProses));
        $responseProses->assertStatus(200);
        $responseProses->assertSee('Tahapan Penanganan Aduan');
        $responseProses->assertSee('Tahap 3 dari 6: Klarifikasi');
        $responseProses->assertSee('Diproses: Klarifikasi');
        $responseProses->assertSee('Klarifikasi Pihak Terkait');

        // 2. Pengajuan Selesai Ditangani
        $pengajuanSelesai = Pengajuan::create([
            'user_id' => $pegawai->id,
            'kategori_id' => $kategori->id,
            'deskripsi' => 'Pintu darurat terkunci.',
            'metode_identitas' => 'identitas',
            'status' => 'selesai',
            'sub_status' => 'selesai_ditangani',
        ]);

        $responseSelesai = $this->actingAs($pegawai)->get(route('pegawai.pengajuan.show', $pengajuanSelesai));
        $responseSelesai->assertStatus(200);
        $responseSelesai->assertSee('Status Akhir: Selesai Ditangani');
        $responseSelesai->assertSee('Hasil Selesai Ditangani');

        // 3. Pengajuan Selesai Ditolak
        $pengajuanDitolak = Pengajuan::create([
            'user_id' => $pegawai->id,
            'kategori_id' => $kategori->id,
            'deskripsi' => 'Permintaan yang melanggar SOP.',
            'metode_identitas' => 'identitas',
            'status' => 'selesai',
            'sub_status' => 'selesai_ditolak',
        ]);

        $responseDitolak = $this->actingAs($pegawai)->get(route('pegawai.pengajuan.show', $pengajuanDitolak));
        $responseDitolak->assertStatus(200);
        $responseDitolak->assertSee('Status Akhir: Selesai Ditolak');
        $responseDitolak->assertSee('Hasil Selesai Ditolak');
    }
}
