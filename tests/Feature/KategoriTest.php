<?php

namespace Tests\Feature;

use App\Models\Kategori;
use App\Models\Pengajuan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class KategoriTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    private User $pegawai;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create([
            'role' => 'admin',
            'is_aktif' => true,
        ]);

        $this->pegawai = User::factory()->create([
            'role' => 'pegawai',
            'is_aktif' => true,
        ]);
    }

    public function test_unauthenticated_user_cannot_access_kategori_management(): void
    {
        $response = $this->get(route('admin.kategori.index'));
        $response->assertRedirect(route('login'));
    }

    public function test_pegawai_cannot_access_kategori_management(): void
    {
        $response = $this->actingAs($this->pegawai)->get(route('admin.kategori.index'));
        $response->assertStatus(403);
    }

    public function test_admin_can_view_kategori_index_with_hierarchy_and_keterangan(): void
    {
        $parent = Kategori::create([
            'nama_kategori' => 'Kendaraan Dinas',
            'keterangan' => 'Pengaduan kelayakan dan operasional kendaraan dinas.',
            'aktif' => true,
        ]);

        $child = Kategori::create([
            'nama_kategori' => 'Roda 2 (R2)',
            'parent_id' => $parent->id,
            'keterangan' => 'Sepeda motor dinas operasional.',
            'aktif' => true,
        ]);

        $response = $this->actingAs($this->admin)->get(route('admin.kategori.index'));

        $response->assertStatus(200);
        $response->assertSee('Kendaraan Dinas');
        $response->assertSee('Pengaduan kelayakan dan operasional kendaraan dinas.');
        $response->assertSee('Roda 2 (R2)');
        $response->assertSee('Sepeda motor dinas operasional.');
    }

    public function test_admin_can_filter_kategori_by_tipe_and_search(): void
    {
        $parent = Kategori::create([
            'nama_kategori' => 'Kendaraan Operasional',
            'keterangan' => 'Pengaduan kendaraan.',
            'aktif' => true,
        ]);

        $child = Kategori::create([
            'nama_kategori' => 'Roda 4 (R4)',
            'parent_id' => $parent->id,
            'keterangan' => 'Mobil dinas operasional.',
            'aktif' => true,
        ]);

        $other = Kategori::create([
            'nama_kategori' => 'Fasilitas Kantor',
            'keterangan' => 'Gedung dan listrik.',
            'aktif' => true,
        ]);

        // Filter tipe parent: should show parent categories, but no child row with "Sub dari:"
        $responseParent = $this->actingAs($this->admin)->get(route('admin.kategori.index', ['tipe' => 'parent']));
        $responseParent->assertSee('Kendaraan Operasional');
        $responseParent->assertSee('Fasilitas Kantor');
        $responseParent->assertDontSee('Sub dari:');

        // Filter tipe child: should show child row with "Sub dari:", but no unrelated parent
        $responseChild = $this->actingAs($this->admin)->get(route('admin.kategori.index', ['tipe' => 'child']));
        $responseChild->assertSee('Roda 4 (R4)');
        $responseChild->assertSee('Sub dari:');
        $responseChild->assertDontSee('Fasilitas Kantor');

        // Search by keyword
        $responseSearch = $this->actingAs($this->admin)->get(route('admin.kategori.index', ['q' => 'Mobil']));
        $responseSearch->assertSee('Roda 4 (R4)');
        $responseSearch->assertDontSee('Fasilitas Kantor');
    }

    public function test_admin_can_create_parent_kategori_with_keterangan(): void
    {
        $response = $this->actingAs($this->admin)->post(route('admin.kategori.store'), [
            'nama' => 'Peralatan Medis',
            'parent_id' => '',
            'keterangan' => 'Kerusakan alat medis poliklinik.',
            'aktif' => '1',
        ]);

        $response->assertRedirect(route('admin.kategori.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('kategori', [
            'nama_kategori' => 'Peralatan Medis',
            'parent_id' => null,
            'keterangan' => 'Kerusakan alat medis poliklinik.',
            'aktif' => 1,
        ]);
    }

    public function test_admin_can_create_child_kategori_with_parent_id_and_keterangan(): void
    {
        $parent = Kategori::create([
            'nama_kategori' => 'Kendaraan Dinas',
            'aktif' => true,
        ]);

        $response = $this->actingAs($this->admin)->post(route('admin.kategori.store'), [
            'nama' => 'Roda 3 (R3)',
            'parent_id' => $parent->id,
            'keterangan' => 'Motor tosa roda 3 angkut dinas.',
            'aktif' => '1',
        ]);

        $response->assertRedirect(route('admin.kategori.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('kategori', [
            'nama_kategori' => 'Roda 3 (R3)',
            'parent_id' => $parent->id,
            'keterangan' => 'Motor tosa roda 3 angkut dinas.',
            'aktif' => 1,
        ]);
    }

    public function test_validation_prevents_duplicate_name_under_same_parent(): void
    {
        $parent = Kategori::create([
            'nama_kategori' => 'Kendaraan',
            'aktif' => true,
        ]);

        Kategori::create([
            'nama_kategori' => 'Roda 2 (R2)',
            'parent_id' => $parent->id,
            'aktif' => true,
        ]);

        $response = $this->actingAs($this->admin)->post(route('admin.kategori.store'), [
            'nama' => 'Roda 2 (R2)',
            'parent_id' => $parent->id,
            'keterangan' => 'Duplikat R2',
        ]);

        $response->assertSessionHasErrors('nama');
    }

    public function test_admin_can_update_kategori_and_its_keterangan(): void
    {
        $parent = Kategori::create([
            'nama_kategori' => 'Sarana',
            'keterangan' => 'Keterangan lama.',
            'aktif' => true,
        ]);

        $response = $this->actingAs($this->admin)->put(route('admin.kategori.update', $parent), [
            'nama' => 'Sarana & Prasarana Baru',
            'parent_id' => '',
            'keterangan' => 'Keterangan baru yang lebih lengkap.',
            'aktif' => '1',
        ]);

        $response->assertRedirect(route('admin.kategori.index'));
        $this->assertDatabaseHas('kategori', [
            'id' => $parent->id,
            'nama_kategori' => 'Sarana & Prasarana Baru',
            'keterangan' => 'Keterangan baru yang lebih lengkap.',
        ]);
    }

    public function test_admin_cannot_set_kategori_as_own_parent(): void
    {
        $parent = Kategori::create([
            'nama_kategori' => 'Kategori X',
            'aktif' => true,
        ]);

        $response = $this->actingAs($this->admin)->put(route('admin.kategori.update', $parent), [
            'nama' => 'Kategori X',
            'parent_id' => $parent->id,
            'aktif' => '1',
        ]);

        $response->assertSessionHas('error');
    }

    public function test_admin_can_delete_kategori_without_pengajuan(): void
    {
        $kategori = Kategori::create([
            'nama_kategori' => 'Kategori Sementara',
            'aktif' => true,
        ]);

        $response = $this->actingAs($this->admin)->delete(route('admin.kategori.destroy', $kategori));

        $response->assertRedirect(route('admin.kategori.index'));
        $this->assertDatabaseMissing('kategori', ['id' => $kategori->id]);
    }

    public function test_admin_deactivates_kategori_when_pengajuan_exists(): void
    {
        $kategori = Kategori::create([
            'nama_kategori' => 'Fasilitas Terkait',
            'aktif' => true,
        ]);

        Pengajuan::create([
            'user_id' => $this->pegawai->id,
            'kategori_id' => $kategori->id,
            'judul' => 'Pengaduan Terkait',
            'isi_laporan' => 'Uraian pengaduan fasilitas.',
            'status' => 'menunggu',
            'is_anonim' => false,
        ]);

        $response = $this->actingAs($this->admin)->delete(route('admin.kategori.destroy', $kategori));

        $response->assertSessionHas('info');
        $this->assertDatabaseHas('kategori', [
            'id' => $kategori->id,
            'aktif' => 0,
        ]);
    }

    public function test_pegawai_sees_parent_kategoris_with_active_children_in_pengajuan_create(): void
    {
        $parent = Kategori::create([
            'nama_kategori' => 'Kendaraan Dinas',
            'keterangan' => 'Cakupan kendaraan.',
            'aktif' => true,
        ]);

        $child = Kategori::create([
            'nama_kategori' => 'Roda 2 (R2)',
            'parent_id' => $parent->id,
            'keterangan' => 'Motor dinas.',
            'aktif' => true,
        ]);

        $response = $this->actingAs($this->pegawai)->get(route('pegawai.pengajuan.create'));

        $response->assertStatus(200);
        $response->assertSee('Kendaraan Dinas');
        $response->assertSee('Roda 2 (R2)');
        $response->assertSee('Cakupan kendaraan.');
    }

    public function test_pegawai_can_create_pengajuan_with_subkategori(): void
    {
        $parent = Kategori::create([
            'nama_kategori' => 'Kendaraan Dinas',
            'aktif' => true,
        ]);

        $child = Kategori::create([
            'nama_kategori' => 'Roda 2 (R2)',
            'parent_id' => $parent->id,
            'aktif' => true,
        ]);

        $response = $this->actingAs($this->pegawai)->post(route('pegawai.pengajuan.store'), [
            'kategori_id' => $child->id,
            'judul' => 'Motor dinas rem blong',
            'isi_laporan' => 'Rem belakang motor dinas tidak berfungsi dengan baik.',
            'is_anonim' => '0',
            'konfirmasi' => '1',
        ]);

        $this->assertDatabaseHas('pengajuan', [
            'kategori_id' => $child->id,
            'deskripsi' => 'Rem belakang motor dinas tidak berfungsi dengan baik.',
        ]);

        $created = Pengajuan::where('deskripsi', 'Rem belakang motor dinas tidak berfungsi dengan baik.')->first();
        $this->assertSame('Kendaraan Dinas — Roda 2 (R2)', $created->nama_kategori_lengkap);
    }

    public function test_pegawai_cannot_submit_parent_kategori_when_children_exist(): void
    {
        $parent = Kategori::create([
            'nama_kategori' => 'Kendaraan Dinas',
            'aktif' => true,
        ]);

        Kategori::create([
            'nama_kategori' => 'Roda 2 (R2)',
            'parent_id' => $parent->id,
            'aktif' => true,
        ]);

        // Attempt to submit parent ID directly when children exist
        $response = $this->actingAs($this->pegawai)->post(route('pegawai.pengajuan.store'), [
            'kategori_id' => $parent->id,
            'judul' => 'Kendaraan bermasalah',
            'isi_laporan' => 'Keluhan terkait kendaraan dinas.',
            'is_anonim' => '0',
            'konfirmasi' => '1',
        ]);

        $response->assertSessionHasErrors('kategori_id');
    }
}
