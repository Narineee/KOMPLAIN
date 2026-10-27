<?php

namespace Tests\Feature;

use App\Models\Kategori;
use App\Models\Pengajuan;
use App\Models\Unit;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AdminPegawaiTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    private Unit $unit;

    protected function setUp(): void
    {
        parent::setUp();

        $this->unit = Unit::create(['nama_unit' => 'Bidang Teknologi Informasi']);

        $this->admin = User::factory()->create([
            'nip' => '198001012005011001',
            'nama' => 'Admin Utama',
            'email' => 'admin@komplain.test',
            'role' => 'admin',
            'unit_id' => $this->unit->id,
            'is_aktif' => true,
        ]);
    }

    public function test_unauthenticated_user_cannot_access_user_management(): void
    {
        $response = $this->get(route('admin.pegawai.index'));

        $response->assertRedirect(route('login'));
    }

    public function test_pegawai_role_cannot_access_user_management(): void
    {
        $pegawai = User::factory()->create([
            'role' => 'pegawai',
            'is_aktif' => true,
        ]);

        $response = $this->actingAs($pegawai)->get(route('admin.pegawai.index'));

        $response->assertStatus(403);
    }

    public function test_admin_can_view_user_management_index(): void
    {
        $targetPegawai = User::factory()->create([
            'nama' => 'Bambang Pamungkas',
            'nip' => '198501012010011001',
            'email' => 'bambang@komplain.test',
            'role' => 'pegawai',
            'unit_id' => $this->unit->id,
            'is_aktif' => true,
        ]);

        $response = $this->actingAs($this->admin)->get(route('admin.pegawai.index'));

        $response->assertStatus(200);
        $response->assertSee('Manajemen Pengguna');
        $response->assertSee('Bambang Pamungkas');
        $response->assertSee('198501012010011001');
        $response->assertSee('bambang@komplain.test');
    }

    public function test_admin_can_filter_users_by_role_and_keyword_and_unit(): void
    {
        $unitA = Unit::create(['nama_unit' => 'Unit Keuangan']);
        $unitB = Unit::create(['nama_unit' => 'Unit Kepegawaian']);

        $pegawaiA = User::factory()->create([
            'nama' => 'Sri Wahyuni',
            'nip' => '199101012016012001',
            'email' => 'sri@komplain.test',
            'role' => 'pegawai',
            'unit_id' => $unitA->id,
            'status_akun' => 'aktif',
        ]);

        $pegawaiB = User::factory()->create([
            'nama' => 'Ahmad Dahlan',
            'nip' => '199201012017011002',
            'email' => 'ahmad@komplain.test',
            'role' => 'pegawai',
            'unit_id' => $unitB->id,
            'status_akun' => 'nonaktif',
        ]);

        // Search by keyword
        $responseKeyword = $this->actingAs($this->admin)->get(route('admin.pegawai.index', ['q' => 'Sri']));
        $responseKeyword->assertSee('Sri Wahyuni');
        $responseKeyword->assertDontSee('Ahmad Dahlan');

        // Filter by unit
        $responseUnit = $this->actingAs($this->admin)->get(route('admin.pegawai.index', ['unit_id' => $unitB->id]));
        $responseUnit->assertSee('Ahmad Dahlan');
        $responseUnit->assertDontSee('Sri Wahyuni');

        // Filter by status
        $responseStatus = $this->actingAs($this->admin)->get(route('admin.pegawai.index', ['status' => 'nonaktif']));
        $responseStatus->assertSee('Ahmad Dahlan');
        $responseStatus->assertDontSee('Sri Wahyuni');
    }

    public function test_admin_can_view_create_user_form(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.pegawai.create'));

        $response->assertStatus(200);
        $response->assertSee('Tambah Akun Pengguna Baru');
        $response->assertSee('NIP Pegawai');
        $response->assertSee('Bidang Teknologi Informasi');
    }

    public function test_admin_can_create_new_pegawai(): void
    {
        $postData = [
            'name' => 'Siti Nurhaliza',
            'nip' => '199501012020012005',
            'email' => 'siti@komplain.test',
            'password' => 'password123',
            'role' => 'pegawai',
            'unit_id' => $this->unit->id,
            'is_aktif' => '1',
        ];

        $response = $this->actingAs($this->admin)->post(route('admin.pegawai.store'), $postData);

        $response->assertRedirect(route('admin.pegawai.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('users', [
            'nip' => '199501012020012005',
            'nama' => 'Siti Nurhaliza',
            'email' => 'siti@komplain.test',
            'role' => 'pegawai',
            'unit_id' => $this->unit->id,
            'status_akun' => 'aktif',
        ]);

        $created = User::where('email', 'siti@komplain.test')->first();
        $this->assertTrue(Hash::check('password123', $created->password));
    }

    public function test_admin_can_create_new_admin_user(): void
    {
        $postData = [
            'name' => 'Admin Baru',
            'nip' => '198701012011011003',
            'email' => 'adminbaru@komplain.test',
            'password' => 'superadmin123',
            'role' => 'admin',
            'unit_id' => null,
            'is_aktif' => '1',
        ];

        $response = $this->actingAs($this->admin)->post(route('admin.pegawai.store'), $postData);

        $response->assertRedirect(route('admin.pegawai.index'));
        $this->assertDatabaseHas('users', [
            'nip' => '198701012011011003',
            'nama' => 'Admin Baru',
            'email' => 'adminbaru@komplain.test',
            'role' => 'admin',
            'status_akun' => 'aktif',
        ]);
    }

    public function test_validation_requires_all_mandatory_fields_on_create(): void
    {
        $response = $this->actingAs($this->admin)->post(route('admin.pegawai.store'), [
            'name' => '',
            'nip' => '',
            'email' => 'not-an-email',
            'password' => 'short',
            'role' => 'invalid-role',
        ]);

        $response->assertSessionHasErrors(['name', 'nip', 'email', 'password', 'role']);
    }

    public function test_validation_rejects_duplicate_nip_or_email(): void
    {
        User::factory()->create([
            'nip' => '198001012005011009',
            'email' => 'existing@komplain.test',
        ]);

        $response = $this->actingAs($this->admin)->post(route('admin.pegawai.store'), [
            'name' => 'User Duplikat',
            'nip' => '198001012005011009',
            'email' => 'existing@komplain.test',
            'password' => 'password123',
            'role' => 'pegawai',
        ]);

        $response->assertSessionHasErrors(['nip', 'email']);
    }

    public function test_admin_can_view_edit_user_form(): void
    {
        $user = User::factory()->create([
            'nama' => 'Joko Susilo',
            'nip' => '198801012012011004',
            'email' => 'joko@komplain.test',
            'unit_id' => $this->unit->id,
            'role' => 'pegawai',
        ]);

        $response = $this->actingAs($this->admin)->get(route('admin.pegawai.edit', $user));

        $response->assertStatus(200);
        $response->assertSee('Edit Data Pengguna');
        $response->assertSee('Joko Susilo');
        $response->assertSee('198801012012011004');
        $response->assertSee('joko@komplain.test');
    }

    public function test_admin_can_update_user_information(): void
    {
        $user = User::factory()->create([
            'nama' => 'Joko Sebelum Update',
            'nip' => '198801012012011004',
            'email' => 'joko@komplain.test',
            'unit_id' => $this->unit->id,
            'role' => 'pegawai',
            'status_akun' => 'aktif',
        ]);

        $updateData = [
            'name' => 'Joko Sesudah Update',
            'nip' => '198801012012011004',
            'email' => 'joko_baru@komplain.test',
            'role' => 'pegawai',
            'unit_id' => null,
            'is_aktif' => '1',
        ];

        $response = $this->actingAs($this->admin)->put(route('admin.pegawai.update', $user), $updateData);

        $response->assertRedirect(route('admin.pegawai.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'nama' => 'Joko Sesudah Update',
            'email' => 'joko_baru@komplain.test',
            'unit_id' => null,
        ]);
    }

    public function test_admin_can_update_user_password(): void
    {
        $user = User::factory()->create([
            'password' => Hash::make('oldpassword'),
        ]);

        $updateData = [
            'name' => $user->name,
            'nip' => $user->nip,
            'email' => $user->email,
            'password' => 'brandnewpassword123',
            'role' => $user->role,
            'is_aktif' => '1',
        ];

        $response = $this->actingAs($this->admin)->put(route('admin.pegawai.update', $user), $updateData);

        $response->assertRedirect(route('admin.pegawai.index'));

        $user->refresh();
        $this->assertTrue(Hash::check('brandnewpassword123', $user->password));
    }

    public function test_admin_cannot_deactivate_or_demote_self_in_update(): void
    {
        // Try deactivating self
        $responseDeactivate = $this->actingAs($this->admin)->put(route('admin.pegawai.update', $this->admin), [
            'name' => $this->admin->name,
            'nip' => $this->admin->nip,
            'email' => $this->admin->email,
            'role' => 'admin',
            'is_aktif' => '0',
        ]);

        $responseDeactivate->assertSessionHas('error', 'Anda tidak dapat menonaktifkan akun Anda sendiri.');
        $this->admin->refresh();
        $this->assertTrue($this->admin->is_aktif);

        // Try demoting self
        $responseDemote = $this->actingAs($this->admin)->put(route('admin.pegawai.update', $this->admin), [
            'name' => $this->admin->name,
            'nip' => $this->admin->nip,
            'email' => $this->admin->email,
            'role' => 'pegawai',
            'is_aktif' => '1',
        ]);

        $responseDemote->assertSessionHas('error', 'Anda tidak dapat mengubah role akun Anda sendiri.');
        $this->admin->refresh();
        $this->assertSame('admin', $this->admin->role);
    }

    public function test_admin_can_toggle_user_status(): void
    {
        $user = User::factory()->create([
            'status_akun' => 'aktif',
        ]);

        $response = $this->actingAs($this->admin)->patch(route('admin.pegawai.toggle-status', $user));

        $response->assertSessionHas('success');
        $user->refresh();
        $this->assertSame('nonaktif', $user->status_akun);

        // Toggle back to active
        $response2 = $this->actingAs($this->admin)->patch(route('admin.pegawai.toggle-status', $user));
        $response2->assertSessionHas('success');
        $user->refresh();
        $this->assertSame('aktif', $user->status_akun);
    }

    public function test_admin_cannot_toggle_self_status(): void
    {
        $response = $this->actingAs($this->admin)->patch(route('admin.pegawai.toggle-status', $this->admin));

        $response->assertSessionHas('error', 'Anda tidak dapat menonaktifkan akun Anda sendiri.');
        $this->admin->refresh();
        $this->assertSame('aktif', $this->admin->status_akun);
    }

    public function test_admin_can_delete_user_without_relations(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($this->admin)->delete(route('admin.pegawai.destroy', $user));

        $response->assertRedirect(route('admin.pegawai.index'));
        $response->assertSessionHas('success');
        $this->assertDatabaseMissing('users', ['id' => $user->id]);
    }

    public function test_admin_cannot_delete_self(): void
    {
        $response = $this->actingAs($this->admin)->delete(route('admin.pegawai.destroy', $this->admin));

        $response->assertSessionHas('error', 'Anda tidak dapat menghapus akun Anda sendiri.');
        $this->assertDatabaseHas('users', ['id' => $this->admin->id]);
    }

    public function test_user_with_pengajuan_or_tanggapan_is_deactivated_instead_of_permanently_deleted(): void
    {
        $user = User::factory()->create(['status_akun' => 'aktif']);
        $kategori = Kategori::create(['nama_kategori' => 'Fasilitas']);

        Pengajuan::create([
            'user_id' => $user->id,
            'kategori_id' => $kategori->id,
            'judul' => 'Laporan uji',
            'isi_laporan' => 'Isi laporan uji',
            'status' => 'diajukan',
            'is_anonim' => false,
        ]);

        $response = $this->actingAs($this->admin)->delete(route('admin.pegawai.destroy', $user));

        $response->assertSessionHas('info');
        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'status_akun' => 'nonaktif',
        ]);
    }

    public function test_pengguna_route_alias_works(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.pengguna.index'));

        $response->assertStatus(200);
        $response->assertSee('Manajemen Pengguna');
    }
}
