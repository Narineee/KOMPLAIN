<?php

namespace Tests\Feature;

use App\Models\Kategori;
use App\Models\Pengajuan;
use App\Models\Unit;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PetugasRoleTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    private User $petugas;

    private Unit $unit;

    private Kategori $kategori;

    protected function setUp(): void
    {
        parent::setUp();

        $this->unit = Unit::create([
            'nama_unit' => 'Bidang Teknologi Informasi',
            'keterangan' => 'Unit penanganan TI',
        ]);

        $this->kategori = Kategori::create([
            'nama_kategori' => 'Infrastruktur Jaringan',
            'aktif' => true,
        ]);

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
    }

    public function test_admin_can_create_petugas_user_with_unit(): void
    {
        $response = $this->actingAs($this->admin)->post(route('admin.pegawai.store'), [
            'name' => 'Ahmad Petugas',
            'email' => 'ahmad@komplain.test',
            'nip' => '199501012020011005',
            'password' => 'password123',
            'role' => 'petugas',
            'unit_id' => $this->unit->id,
            'is_aktif' => 1,
        ]);

        $response->assertRedirect(route('admin.pegawai.index'));
        $this->assertDatabaseHas('users', [
            'email' => 'ahmad@komplain.test',
            'role' => 'petugas',
            'unit_id' => $this->unit->id,
        ]);
    }

    public function test_admin_cannot_create_petugas_user_without_unit(): void
    {
        $response = $this->actingAs($this->admin)->post(route('admin.pegawai.store'), [
            'name' => 'Petugas Tanpa Unit',
            'email' => 'petugastanpaunit@komplain.test',
            'nip' => '199501012020011006',
            'password' => 'password123',
            'role' => 'petugas',
            'unit_id' => null,
            'is_aktif' => 1,
        ]);

        $response->assertSessionHasErrors('unit_id');
    }

    public function test_admin_can_assign_complaint_to_unit_and_petugas(): void
    {
        $pelapor = User::factory()->create(['role' => 'pegawai']);
        $pengajuan = Pengajuan::create([
            'user_id' => $pelapor->id,
            'kategori_id' => $this->kategori->id,
            'deskripsi' => 'Internet di lantai 3 terputus sejak pagi.',
            'metode_identitas' => 'identitas',
            'status' => 'belum_verifikasi',
        ]);

        $response = $this->actingAs($this->admin)->patch(route('admin.laporan.tugaskan', $pengajuan), [
            'assigned_unit_id' => $this->unit->id,
            'petugas_id' => $this->petugas->id,
            'catatan_penugasan' => 'Tolong segera dicek router mikrotik lantai 3.',
        ]);

        $response->assertSessionHas('success');
        $pengajuan->refresh();

        $this->assertEquals($this->unit->id, $pengajuan->assigned_unit_id);
        $this->assertEquals($this->petugas->id, $pengajuan->petugas_id);
        $this->assertEquals('diproses', $pengajuan->status);
        $this->assertEquals('identifikasi_klarifikasi', $pengajuan->sub_status);
        $this->assertEquals('Tolong segera dicek router mikrotik lantai 3.', $pengajuan->catatan_penugasan);

        $this->assertDatabaseHas('riwayat_status', [
            'pengajuan_id' => $pengajuan->id,
            'status_baru' => 'diproses',
        ]);

        $this->assertDatabaseHas('notifikasi', [
            'user_id' => $this->petugas->id,
            'pengajuan_id' => $pengajuan->id,
            'tipe' => 'penugasan_baru',
        ]);
    }

    public function test_petugas_can_view_assigned_complaint(): void
    {
        $pelapor = User::factory()->create(['role' => 'pegawai']);
        $pengajuan = Pengajuan::create([
            'user_id' => $pelapor->id,
            'kategori_id' => $this->kategori->id,
            'deskripsi' => 'Printer tidak merespon di ruang rapat.',
            'metode_identitas' => 'identitas',
            'status' => 'diproses',
            'assigned_unit_id' => $this->unit->id,
            'petugas_id' => $this->petugas->id,
        ]);

        // Akses dashboard petugas
        $responseDashboard = $this->actingAs($this->petugas)->get(route('petugas.dashboard'));
        $responseDashboard->assertOk();
        $responseDashboard->assertSee('Printer tidak merespon');

        // Akses index petugas
        $responseIndex = $this->actingAs($this->petugas)->get(route('petugas.pengajuan.index'));
        $responseIndex->assertOk();
        $responseIndex->assertSee('Printer tidak merespon');

        // Akses detail petugas
        $responseShow = $this->actingAs($this->petugas)->get(route('petugas.pengajuan.show', $pengajuan));
        $responseShow->assertOk();
        $responseShow->assertSee('Printer tidak merespon');
        $responseShow->assertSee($pelapor->name);
    }

    public function test_petugas_cannot_view_unassigned_complaint_of_another_unit(): void
    {
        $unitLain = Unit::create(['nama_unit' => 'Bidang Keuangan']);
        $pelapor = User::factory()->create(['role' => 'pegawai']);

        $pengajuanLain = Pengajuan::create([
            'user_id' => $pelapor->id,
            'kategori_id' => $this->kategori->id,
            'deskripsi' => 'Masalah anggaran operasional.',
            'metode_identitas' => 'identitas',
            'status' => 'diproses',
            'assigned_unit_id' => $unitLain->id,
            'petugas_id' => null,
        ]);

        $response = $this->actingAs($this->petugas)->get(route('petugas.pengajuan.show', $pengajuanLain));
        $response->assertStatus(403);
    }

    public function test_petugas_cannot_see_anonymous_reporter_identity(): void
    {
        $pelapor = User::factory()->create([
            'name' => 'Budi Rahasia',
            'nip' => '198888888888888888',
            'role' => 'pegawai',
        ]);

        $pengajuanAnonim = Pengajuan::create([
            'user_id' => $pelapor->id,
            'kategori_id' => $this->kategori->id,
            'deskripsi' => 'Pengaduan sensitif anonim.',
            'metode_identitas' => 'anonim',
            'status' => 'diproses',
            'assigned_unit_id' => $this->unit->id,
            'petugas_id' => $this->petugas->id,
        ]);

        $response = $this->actingAs($this->petugas)->get(route('petugas.pengajuan.show', $pengajuanAnonim));
        $response->assertOk();
        $response->assertSee('Laporan Anonim');
        $response->assertDontSee('Budi Rahasia');
        $response->assertDontSee('198888888888888888');
    }

    public function test_petugas_can_still_access_pegawai_portal_and_submit_complaint(): void
    {
        // Petugas mengakses portal pegawai miliknya
        $responseDashboardPegawai = $this->actingAs($this->petugas)->get(route('pegawai.dashboard'));
        $responseDashboardPegawai->assertOk();

        // Petugas membuat laporan aduan pribadi
        $responseForm = $this->actingAs($this->petugas)->get(route('pegawai.pengajuan.create'));
        $responseForm->assertOk();

        $responseStore = $this->actingAs($this->petugas)->post(route('pegawai.pengajuan.store'), [
            'kategori_id' => $this->kategori->id,
            'isi_laporan' => 'AC di ruang kerja saya mati total sejak tadi pagi.',
            'is_anonim' => 0,
        ]);

        $responseStore->assertSessionHasNoErrors();

        $this->assertDatabaseHas('pengajuan', [
            'user_id' => $this->petugas->id,
            'deskripsi' => 'AC di ruang kerja saya mati total sejak tadi pagi.',
        ]);
    }

    public function test_petugas_can_update_complaint_status_and_submit_tanggapan(): void
    {
        $pelapor = User::factory()->create(['role' => 'pegawai']);
        $pengajuan = Pengajuan::create([
            'user_id' => $pelapor->id,
            'kategori_id' => $this->kategori->id,
            'deskripsi' => 'Gangguan WiFi lantai 2.',
            'metode_identitas' => 'identitas',
            'status' => 'diproses',
            'sub_status' => 'identifikasi_klarifikasi',
            'assigned_unit_id' => $this->unit->id,
            'petugas_id' => $this->petugas->id,
        ]);

        // Update tahapan penanganan
        $responseUpdate = $this->actingAs($this->petugas)->patch(route('petugas.pengajuan.status', $pengajuan), [
            'tahap' => 'rencana_aksi',
            'catatan' => 'Akan diganti kabel switch sore ini.',
        ]);

        $responseUpdate->assertSessionHas('success');
        $pengajuan->refresh();

        $this->assertEquals('rencana_aksi', $pengajuan->sub_status);

        // Kirim tanggapan resmi
        $responseTanggapan = $this->actingAs($this->petugas)->post(route('petugas.pengajuan.tanggapan', $pengajuan), [
            'pesan' => 'Sedang dalam penanganan teknisi kami.',
            'is_internal' => 0,
        ]);

        $responseTanggapan->assertSessionHas('success');

        $this->assertDatabaseHas('tanggapan', [
            'pengajuan_id' => $pengajuan->id,
            'admin_id' => $this->petugas->id,
            'isi_tanggapan' => 'Sedang dalam penanganan teknisi kami.',
        ]);

        $this->assertDatabaseHas('notifikasi', [
            'user_id' => $pelapor->id,
            'pengajuan_id' => $pengajuan->id,
            'tipe' => 'tanggapan_baru',
        ]);
    }

    public function test_petugas_can_create_and_view_own_complaint_in_petugas_panel(): void
    {
        // 1. Petugas buka form buat aduan panel petugas
        $responseForm = $this->actingAs($this->petugas)->get(route('petugas.aduan.create'));
        $responseForm->assertOk();
        $responseForm->assertSee('Sampaikan aduan Anda');

        // 2. Submit aduan
        $responseStore = $this->actingAs($this->petugas)->post(route('petugas.aduan.store'), [
            'kategori_id' => $this->kategori->id,
            'isi_laporan' => 'Pendingin ruangan mati total di ruang petugas bidang.',
            'is_anonim' => 0,
        ]);

        $responseStore->assertRedirect(route('petugas.aduan.index'));
        $responseStore->assertSessionHas('success');

        $this->assertDatabaseHas('pengajuan', [
            'user_id' => $this->petugas->id,
            'deskripsi' => 'Pendingin ruangan mati total di ruang petugas bidang.',
        ]);

        // 3. Cek halaman Aduan Saya (termasuk notice ketentuan keberatan)
        $responseIndex = $this->actingAs($this->petugas)->get(route('petugas.aduan.index'));
        $responseIndex->assertOk();
        $responseIndex->assertSee('Pendingin ruangan mati total');
        $responseIndex->assertSee('Ketentuan Keberatan Terhadap Hasil Pengaduan');
        $responseIndex->assertSee('maksimal 1 (satu) kali', false);

        // 4. Cek detail aduan saya
        $pengajuan = Pengajuan::where('user_id', $this->petugas->id)->first();
        $responseShow = $this->actingAs($this->petugas)->get(route('petugas.aduan.show', $pengajuan));
        $responseShow->assertOk();
        $responseShow->assertSee('Pendingin ruangan mati total');
    }

    public function test_petugas_can_create_complaint_with_subcategory(): void
    {
        $parent = Kategori::create([
            'nama_kategori' => 'Kendaraan Dinas',
            'aktif' => true,
        ]);

        $child = Kategori::create([
            'parent_id' => $parent->id,
            'nama_kategori' => 'Roda 4 (Mobil)',
            'aktif' => true,
        ]);

        // Submit dengan child kategori_id
        $response = $this->actingAs($this->petugas)->post(route('petugas.aduan.store'), [
            'kategori_id' => $child->id,
            'isi_laporan' => 'Ban mobil dinas bocor di halaman parkir barat.',
            'is_anonim' => 0,
        ]);

        $response->assertRedirect(route('petugas.aduan.index'));
        $this->assertDatabaseHas('pengajuan', [
            'user_id' => $this->petugas->id,
            'kategori_id' => $child->id,
            'deskripsi' => 'Ban mobil dinas bocor di halaman parkir barat.',
        ]);
    }

    public function test_petugas_can_submit_keberatan_in_own_complaint(): void
    {
        $pengajuan = Pengajuan::create([
            'user_id' => $this->petugas->id,
            'kategori_id' => $this->kategori->id,
            'deskripsi' => 'Kipas angin rusak dan mengeluarkan asap.',
            'metode_identitas' => 'identitas',
            'status' => 'selesai',
            'tanggal_selesai' => now(),
            'konfirmasi_pelapor' => null,
            'jumlah_keberatan' => 0,
        ]);

        // Form keberatan
        $responseForm = $this->actingAs($this->petugas)->get(route('petugas.aduan.keberatan.form', $pengajuan));
        $responseForm->assertOk();
        $responseForm->assertSee('Alasan keberatan');

        // Submit keberatan
        $responsePost = $this->actingAs($this->petugas)->post(route('petugas.aduan.keberatan', $pengajuan), [
            'alasan' => 'Masih berasap dan belum diperbaiki kabelnya dengan baik.',
        ]);

        $responsePost->assertRedirect(route('petugas.aduan.show', $pengajuan));
        $responsePost->assertSessionHas('success');

        $pengajuan->refresh();
        $this->assertEquals('diproses', $pengajuan->status);
        $this->assertEquals('keberatan', $pengajuan->konfirmasi_pelapor);
        $this->assertEquals(1, $pengajuan->jumlah_keberatan);
    }

    public function test_petugas_aduan_detail_does_not_show_pdf_button(): void
    {
        $pengajuan = Pengajuan::create([
            'user_id' => $this->petugas->id,
            'kategori_id' => $this->kategori->id,
            'deskripsi' => 'Pengaduan fasilitas dari petugas.',
            'metode_identitas' => 'identitas',
            'status' => 'menunggu',
        ]);

        $response = $this->actingAs($this->petugas)->get(route('petugas.aduan.show', $pengajuan));

        $response->assertStatus(200);
        $response->assertDontSee('Cetak Bukti (PDF)');
    }

    public function test_petugas_aduan_pdf_route_is_removed(): void
    {
        $pengajuan = Pengajuan::create([
            'user_id' => $this->petugas->id,
            'kategori_id' => $this->kategori->id,
            'deskripsi' => 'Pengaduan fasilitas dari petugas.',
            'metode_identitas' => 'identitas',
            'status' => 'menunggu',
        ]);

        $response = $this->actingAs($this->petugas)->get("/petugas/aduan/{$pengajuan->id}/pdf");

        $response->assertStatus(404);
    }

    public function test_petugas_completing_complaint_synchronizes_konfirmasi_pelapor_and_tanggal_selesai(): void
    {
        $pelapor = User::factory()->create(['role' => 'pegawai']);
        $pengajuan = Pengajuan::create([
            'user_id' => $pelapor->id,
            'kategori_id' => $this->kategori->id,
            'deskripsi' => 'Printer kantor rusak.',
            'status' => 'diproses',
            'sub_status' => 'aksi',
            'assigned_unit_id' => $this->unit->id,
            'petugas_id' => $this->petugas->id,
        ]);

        // Petugas menyelesaikan laporan
        $response = $this->actingAs($this->petugas)->patch(route('petugas.pengajuan.status', $pengajuan), [
            'tahap' => 'selesai',
            'catatan' => 'Printer telah diperbaiki dan diganti roll kertasnya.',
        ]);

        $response->assertSessionHas('success');
        $pengajuan->refresh();

        $this->assertSame('selesai', $pengajuan->status);
        $this->assertNull($pengajuan->sub_status);
        $this->assertNull($pengajuan->konfirmasi_pelapor);
        $this->assertNotNull($pengajuan->tanggal_selesai);
        $this->assertTrue($pengajuan->perlu_konfirmasi);
    }

    public function test_petugas_completing_review_complaint_sets_selesai_tinjau_ulang(): void
    {
        $pelapor = User::factory()->create(['role' => 'pegawai']);
        $pengajuan = Pengajuan::create([
            'user_id' => $pelapor->id,
            'kategori_id' => $this->kategori->id,
            'deskripsi' => 'AC kembali mati setelah diperbaiki.',
            'status' => 'diproses',
            'sub_status' => 'identifikasi_klarifikasi',
            'konfirmasi_pelapor' => 'keberatan',
            'jumlah_keberatan' => 1,
            'assigned_unit_id' => $this->unit->id,
            'petugas_id' => $this->petugas->id,
        ]);

        // Petugas menyelesaikan laporan tinjau ulang
        $response = $this->actingAs($this->petugas)->patch(route('petugas.pengajuan.status', $pengajuan), [
            'tahap' => 'selesai',
            'catatan' => 'Freon telah diisi ulang dan kompresor diganti.',
        ]);

        $response->assertSessionHas('success');
        $pengajuan->refresh();

        $this->assertSame('selesai', $pengajuan->status);
        $this->assertSame('selesai_setelah_tinjau_ulang', $pengajuan->sub_status);
        $this->assertSame('selesai_tinjau_ulang', $pengajuan->konfirmasi_pelapor);
        $this->assertNotNull($pengajuan->dikonfirmasi_pada);
        $this->assertNotNull($pengajuan->tanggal_selesai);
        $this->assertFalse($pengajuan->perlu_konfirmasi);
    }

    public function test_petugas_moving_status_from_selesai_back_to_diproses_clears_tanggal_selesai(): void
    {
        $pelapor = User::factory()->create(['role' => 'pegawai']);
        $pengajuan = Pengajuan::create([
            'user_id' => $pelapor->id,
            'kategori_id' => $this->kategori->id,
            'deskripsi' => 'Pengaduan yang sebelumnya selesai.',
            'status' => 'selesai',
            'tanggal_selesai' => now(),
            'assigned_unit_id' => $this->unit->id,
            'petugas_id' => $this->petugas->id,
        ]);

        // Petugas mengembalikan status ke aksi
        $response = $this->actingAs($this->petugas)->patch(route('petugas.pengajuan.status', $pengajuan), [
            'tahap' => 'aksi',
            'catatan' => 'Dikerjakan kembali karena ada temuan tambahan.',
        ]);

        $response->assertSessionHas('success');
        $pengajuan->refresh();

        $this->assertSame('diproses', $pengajuan->status);
        $this->assertSame('aksi', $pengajuan->sub_status);
        $this->assertNull($pengajuan->tanggal_selesai);
    }
}
