<?php

namespace Tests\Feature;

use App\Models\Kategori;
use App\Models\Pengajuan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminLaporanTest extends TestCase
{
    use RefreshDatabase;

    public function test_pegawai_cannot_access_admin_dashboard(): void
    {
        $pegawai = User::factory()->create(['role' => 'pegawai', 'is_aktif' => true]);

        $response = $this->actingAs($pegawai)->get(route('admin.dashboard'));

        $response->assertStatus(403);
    }

    public function test_admin_can_access_admin_dashboard(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'is_aktif' => true]);

        $response = $this->actingAs($admin)->get(route('admin.dashboard'));

        $response->assertStatus(200);
    }

    public function test_admin_can_update_complaint_status(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'is_aktif' => true]);
        $kategori = Kategori::create(['nama' => 'Pelayanan', 'is_aktif' => true]);

        $pengajuan = Pengajuan::create([
            'nomor_tiket' => 'TKT-20261009-ADM01',
            'user_id' => null,
            'kategori_id' => $kategori->id,
            'judul' => 'Keluhan Sarana',
            'isi_laporan' => 'Isi laporan sarana.',
            'is_anonim' => true,
            'status' => 'diajukan',
        ]);

        $response = $this->actingAs($admin)->patch(route('admin.laporan.status', $pengajuan), [
            'status' => 'diproses',
            'catatan' => 'Laporan sedang ditindaklanjuti oleh tim teknis.',
        ]);

        $response->assertRedirect();
        $this->assertEquals('diproses', $pengajuan->fresh()->status);
        $this->assertDatabaseHas('riwayat_statuses', [
            'pengajuan_id' => $pengajuan->id,
            'user_id' => $admin->id,
            'status_sebelumnya' => 'diajukan',
            'status_baru' => 'diproses',
        ]);
    }

    public function test_admin_can_add_response_to_complaint(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'is_aktif' => true]);
        $kategori = Kategori::create(['nama' => 'Pelayanan', 'is_aktif' => true]);

        $pengajuan = Pengajuan::create([
            'nomor_tiket' => 'TKT-20261009-ADM02',
            'user_id' => null,
            'kategori_id' => $kategori->id,
            'judul' => 'Keluhan Sarana 2',
            'isi_laporan' => 'Isi laporan sarana 2.',
            'is_anonim' => true,
            'status' => 'diproses',
        ]);

        $response = $this->actingAs($admin)->post(route('admin.laporan.tanggapan', $pengajuan), [
            'pesan' => 'Petugas telah dikerahkan ke lokasi untuk perbaikan.',
            'is_internal' => '0',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('tanggapans', [
            'pengajuan_id' => $pengajuan->id,
            'user_id' => $admin->id,
            'pesan' => 'Petugas telah dikerahkan ke lokasi untuk perbaikan.',
            'is_internal' => false,
        ]);
    }
}
