<?php

namespace Tests\Feature;

use App\Models\Kategori;
use App\Models\Pengajuan;
use App\Models\Unit;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminUnitTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    private User $petugas;

    private User $pegawai;

    private Unit $unit;

    protected function setUp(): void
    {
        parent::setUp();

        $this->unit = Unit::create(['nama_unit' => 'Bidang Komunikasi dan Informatika']);

        $this->admin = User::factory()->create([
            'role' => 'admin',
            'unit_id' => $this->unit->id,
            'is_aktif' => true,
        ]);

        $this->petugas = User::factory()->create([
            'role' => 'petugas',
            'unit_id' => $this->unit->id,
            'is_aktif' => true,
        ]);

        $this->pegawai = User::factory()->create([
            'role' => 'pegawai',
            'unit_id' => $this->unit->id,
            'is_aktif' => true,
        ]);
    }

    public function test_unauthenticated_user_cannot_access_bidang_management(): void
    {
        $response = $this->get(route('admin.unit.index'));
        $response->assertRedirect(route('login'));
    }

    public function test_pegawai_and_petugas_cannot_access_bidang_management(): void
    {
        $this->actingAs($this->pegawai)
            ->get(route('admin.unit.index'))
            ->assertForbidden();

        $this->actingAs($this->petugas)
            ->get(route('admin.unit.index'))
            ->assertForbidden();
    }

    public function test_admin_can_view_bidang_list_and_counts(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.unit.index'));

        $response->assertOk();
        $response->assertSee('Kelola Bidang Tugas');
        $response->assertSee('Bidang Komunikasi dan Informatika');
    }

    public function test_admin_can_create_new_bidang(): void
    {
        $response = $this->actingAs($this->admin)->post(route('admin.unit.store'), [
            'nama_unit' => 'Bidang Statistik dan Persandian',
        ]);

        $response->assertRedirect(route('admin.unit.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('unit', [
            'nama_unit' => 'Bidang Statistik dan Persandian',
        ]);
    }

    public function test_admin_cannot_create_duplicate_bidang(): void
    {
        $response = $this->actingAs($this->admin)->post(route('admin.unit.store'), [
            'nama_unit' => 'Bidang Komunikasi dan Informatika',
        ]);

        $response->assertSessionHasErrors(['nama_unit']);
    }

    public function test_admin_can_edit_bidang(): void
    {
        $response = $this->actingAs($this->admin)->put(route('admin.unit.update', $this->unit), [
            'nama_unit' => 'Bidang Kominfo dan Kehumasan',
        ]);

        $response->assertRedirect(route('admin.unit.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('unit', [
            'id' => $this->unit->id,
            'nama_unit' => 'Bidang Kominfo dan Kehumasan',
        ]);
    }

    public function test_admin_cannot_delete_bidang_that_has_users(): void
    {
        $response = $this->actingAs($this->admin)->delete(route('admin.unit.destroy', $this->unit));

        $response->assertRedirect(route('admin.unit.index'));
        $response->assertSessionHas('error');

        $this->assertDatabaseHas('unit', [
            'id' => $this->unit->id,
        ]);
    }

    public function test_admin_cannot_delete_bidang_that_has_assigned_pengaduan(): void
    {
        $emptyUnit = Unit::create(['nama_unit' => 'Bidang Pengawasan']);
        $kategori = Kategori::create(['nama' => 'Teknis']);

        Pengajuan::create([
            'user_id' => $this->pegawai->id,
            'kategori_id' => $kategori->id,
            'isi_laporan' => 'Perlu pengecekan berkala perangkat dinas.',
            'status' => 'diproses',
            'assigned_unit_id' => $emptyUnit->id,
            'nomor_tiket' => 'TIK-TEST-001',
        ]);

        $response = $this->actingAs($this->admin)->delete(route('admin.unit.destroy', $emptyUnit));

        $response->assertRedirect(route('admin.unit.index'));
        $response->assertSessionHas('error');

        $this->assertDatabaseHas('unit', [
            'id' => $emptyUnit->id,
        ]);
    }

    public function test_admin_can_delete_unused_bidang(): void
    {
        $emptyUnit = Unit::create(['nama_unit' => 'Bidang Sementara']);

        $response = $this->actingAs($this->admin)->delete(route('admin.unit.destroy', $emptyUnit));

        $response->assertRedirect(route('admin.unit.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseMissing('unit', [
            'id' => $emptyUnit->id,
        ]);
    }
}
