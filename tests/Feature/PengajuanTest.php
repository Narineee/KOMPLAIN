<?php

namespace Tests\Feature;

use App\Models\Kategori;
use App\Models\Pengajuan;
use App\Models\RiwayatStatus;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
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
            'status' => 'belum_verifikasi',
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
            'sub_status' => 'identifikasi_klarifikasi',
        ]);

        $responseProses = $this->actingAs($pegawai)->get(route('pegawai.pengajuan.show', $pengajuanProses));
        $responseProses->assertStatus(200);
        $responseProses->assertSee('Progres pengaduan');
        $responseProses->assertSee('Identifikasi / Klarifikasi (Analisis masalah)');
        $responseProses->assertSee('Aduan Anda sedang ditangani');

        // 2. Pengaduan Selesai
        $pengajuanSelesai = Pengajuan::create([
            'user_id' => $pegawai->id,
            'kategori_id' => $kategori->id,
            'deskripsi' => 'Pintu darurat terkunci.',
            'metode_identitas' => 'identitas',
            'status' => 'selesai',
            'konfirmasi_pelapor' => 'dikonfirmasi',
        ]);

        $responseSelesai = $this->actingAs($pegawai)->get(route('pegawai.pengajuan.show', $pengajuanSelesai));
        $responseSelesai->assertStatus(200);
        $responseSelesai->assertSee('Aduan sudah selesai ditangani');
        $responseSelesai->assertSee('Selesai');
    }

    public function test_pegawai_can_access_dashboard_and_views_waiting_confirmation_complaints(): void
    {
        $pegawai = User::factory()->create(['role' => 'pegawai', 'is_aktif' => true]);
        $kategori = Kategori::create(['nama_kategori' => 'Fasilitas', 'aktif' => true]);

        Pengajuan::create([
            'user_id' => $pegawai->id,
            'kategori_id' => $kategori->id,
            'deskripsi' => 'Pengaduan yang sudah selesai tapi butuh konfirmasi',
            'metode_identitas' => 'identitas',
            'status' => 'selesai',
            'sub_status' => 'selesai_ditangani',
            'konfirmasi_pelapor' => null,
            'tanggal_selesai' => now(),
        ]);

        $response = $this->actingAs($pegawai)->get(route('pegawai.dashboard'));
        $response->assertStatus(200);
        $response->assertSee('aduan menunggu konfirmasi Anda');
        $response->assertSee('Pengaduan yang sudah selesai');
    }

    public function test_pegawai_can_confirm_completed_complaint(): void
    {
        $pegawai = User::factory()->create(['role' => 'pegawai', 'is_aktif' => true]);
        $kategori = Kategori::create(['nama_kategori' => 'Fasilitas', 'aktif' => true]);

        $pengajuan = Pengajuan::create([
            'user_id' => $pegawai->id,
            'kategori_id' => $kategori->id,
            'deskripsi' => 'Pengaduan selesai butuh konfirmasi',
            'metode_identitas' => 'identitas',
            'status' => 'selesai',
            'sub_status' => 'selesai_ditangani',
            'konfirmasi_pelapor' => null,
            'tanggal_selesai' => now(),
        ]);

        $response = $this->actingAs($pegawai)->post(route('pegawai.pengajuan.konfirmasi', $pengajuan));
        $response->assertRedirect();
        $response->assertSessionHas('success');

        $pengajuan->refresh();
        $this->assertEquals('dikonfirmasi', $pengajuan->konfirmasi_pelapor);
        $this->assertNotNull($pengajuan->dikonfirmasi_pada);
        $this->assertFalse($pengajuan->perlu_konfirmasi);
    }

    public function test_pegawai_can_view_keberatan_form(): void
    {
        $pegawai = User::factory()->create(['role' => 'pegawai', 'is_aktif' => true]);
        $kategori = Kategori::create(['nama_kategori' => 'Fasilitas', 'aktif' => true]);

        $pengajuan = Pengajuan::create([
            'user_id' => $pegawai->id,
            'kategori_id' => $kategori->id,
            'deskripsi' => 'Pengaduan selesai butuh konfirmasi',
            'metode_identitas' => 'identitas',
            'status' => 'selesai',
            'sub_status' => 'selesai_ditangani',
            'konfirmasi_pelapor' => null,
            'tanggal_selesai' => now(),
        ]);

        $response = $this->actingAs($pegawai)->get(route('pegawai.pengajuan.keberatan.form', $pengajuan));

        $response->assertStatus(200);
        $response->assertSee('Mari tinjau kembali');
        $response->assertSee('Aduan yang ditinjau');
        $response->assertSee('Alasan keberatan');
        $response->assertSee('Lampiran baru (opsional)');
        $response->assertSee('Tambah lampiran pendukung');
        $response->assertSee('Aduan akan berstatus Ditinjau ulang');
        $response->assertSee('Kirim keberatan');
        $response->assertSee('Batalkan');
    }

    public function test_pegawai_can_object_completed_complaint_with_attachment(): void
    {
        Storage::fake('public');

        $pegawai = User::factory()->create(['role' => 'pegawai', 'is_aktif' => true]);
        $kategori = Kategori::create(['nama_kategori' => 'Fasilitas', 'aktif' => true]);

        $pengajuan = Pengajuan::create([
            'user_id' => $pegawai->id,
            'kategori_id' => $kategori->id,
            'deskripsi' => 'Pengaduan selesai tapi belum beres',
            'metode_identitas' => 'identitas',
            'status' => 'selesai',
            'sub_status' => 'selesai_ditangani',
            'konfirmasi_pelapor' => null,
            'tanggal_selesai' => now(),
        ]);

        $file = UploadedFile::fake()->create('suhu-ruangan-15-okt.jpg', 1200, 'image/jpeg');

        $response = $this->actingAs($pegawai)->post(route('pegawai.pengajuan.keberatan', $pengajuan), [
            'alasan' => 'Masalah AC masih tetap bocor dan belum dingin.',
            'lampiran' => [$file],
        ]);

        $response->assertRedirect(route('pegawai.pengajuan.show', $pengajuan));
        $response->assertSessionHas('success');

        $pengajuan->refresh();
        $this->assertEquals('diproses', $pengajuan->status);
        $this->assertEquals('identifikasi_klarifikasi', $pengajuan->sub_status);
        $this->assertNull($pengajuan->tanggal_selesai);
        $this->assertEquals('keberatan', $pengajuan->konfirmasi_pelapor);
        $this->assertEquals(1, $pengajuan->jumlah_keberatan);
        $this->assertFalse($pengajuan->perlu_konfirmasi);

        $this->assertDatabaseHas('lampiran', [
            'pengajuan_id' => $pengajuan->id,
            'nama_file' => 'suhu-ruangan-15-okt.jpg',
        ]);
    }

    public function test_pegawai_can_object_completed_complaint(): void
    {
        $pegawai = User::factory()->create(['role' => 'pegawai', 'is_aktif' => true]);
        $kategori = Kategori::create(['nama_kategori' => 'Fasilitas', 'aktif' => true]);

        $pengajuan = Pengajuan::create([
            'user_id' => $pegawai->id,
            'kategori_id' => $kategori->id,
            'deskripsi' => 'Pengaduan selesai tapi belum beres',
            'metode_identitas' => 'identitas',
            'status' => 'selesai',
            'konfirmasi_pelapor' => null,
            'tanggal_selesai' => now(),
        ]);

        $response = $this->actingAs($pegawai)->post(route('pegawai.pengajuan.keberatan', $pengajuan), [
            'alasan' => 'Masalah AC masih tetap bocor dan belum dingin.',
        ]);

        $response->assertRedirect(route('pegawai.pengajuan.show', $pengajuan));
        $response->assertSessionHas('success');

        $pengajuan->refresh();
        $this->assertEquals('diproses', $pengajuan->status);
        $this->assertEquals('identifikasi_klarifikasi', $pengajuan->sub_status);
        $this->assertNull($pengajuan->tanggal_selesai);
        $this->assertEquals('keberatan', $pengajuan->konfirmasi_pelapor);
        $this->assertEquals(1, $pengajuan->jumlah_keberatan);
        $this->assertFalse($pengajuan->perlu_konfirmasi);
    }

    public function test_pegawai_cannot_object_more_than_once(): void
    {
        $pegawai = User::factory()->create(['role' => 'pegawai', 'is_aktif' => true]);
        $kategori = Kategori::create(['nama_kategori' => 'Fasilitas', 'aktif' => true]);

        $pengajuan = Pengajuan::create([
            'user_id' => $pegawai->id,
            'kategori_id' => $kategori->id,
            'deskripsi' => 'Pengaduan yang sudah pernah diajukan keberatan',
            'metode_identitas' => 'identitas',
            'status' => 'selesai',
            'sub_status' => 'selesai_setelah_tinjau_ulang',
            'konfirmasi_pelapor' => 'selesai_tinjau_ulang',
            'jumlah_keberatan' => 1,
            'tanggal_selesai' => now(),
        ]);

        $responseForm = $this->actingAs($pegawai)->get(route('pegawai.pengajuan.keberatan.form', $pengajuan));
        $responseForm->assertRedirect(route('pegawai.pengajuan.show', $pengajuan));
        $responseForm->assertSessionHas('error');

        $responsePost = $this->actingAs($pegawai)->post(route('pegawai.pengajuan.keberatan', $pengajuan), [
            'alasan' => 'Mencoba mengajukan keberatan kedua kalinya.',
        ]);
        $responsePost->assertRedirect(route('pegawai.pengajuan.show', $pengajuan));
        $responsePost->assertSessionHas('error');

        $pengajuan->refresh();
        $this->assertEquals(1, $pengajuan->jumlah_keberatan);
    }

    public function test_dashboard_displays_keberatan_rule_notice(): void
    {
        $pegawai = User::factory()->create(['role' => 'pegawai', 'is_aktif' => true]);

        $response = $this->actingAs($pegawai)->get(route('pegawai.dashboard'));

        $response->assertStatus(200);
        $response->assertSee('Ketentuan Keberatan Terhadap Hasil Pengaduan');
        $response->assertSee('maksimal 1 (satu) kali', false);
        $response->assertSee('prosedur aduan baru');
    }

    public function test_pegawai_can_view_index_with_status_filter(): void
    {
        $pegawai = User::factory()->create(['role' => 'pegawai', 'is_aktif' => true]);
        $kategori = Kategori::create(['nama_kategori' => 'Fasilitas', 'aktif' => true]);

        Pengajuan::create([
            'user_id' => $pegawai->id,
            'kategori_id' => $kategori->id,
            'deskripsi' => 'Pengaduan status baru',
            'metode_identitas' => 'identitas',
            'status' => 'menunggu',
        ]);

        $response = $this->actingAs($pegawai)->get(route('pegawai.pengajuan.index', ['status' => 'baru']));
        $response->assertStatus(200);
        $response->assertSee('Pengaduan status baru');
    }

    public function test_pegawai_can_view_detail_of_complaint_under_review(): void
    {
        $pegawai = User::factory()->create(['role' => 'pegawai', 'is_aktif' => true]);
        $kategori = Kategori::create(['nama_kategori' => 'Sarana & prasarana', 'aktif' => true]);

        $pengajuan = Pengajuan::create([
            'user_id' => $pegawai->id,
            'kategori_id' => $kategori->id,
            'deskripsi' => 'AC di ruang rapat B tidak mengeluarkan udara dingin.',
            'metode_identitas' => 'identitas',
            'status' => 'diproses',
            'sub_status' => 'identifikasi_klarifikasi',
            'konfirmasi_pelapor' => 'keberatan',
            'dikonfirmasi_pada' => now(),
        ]);

        RiwayatStatus::create([
            'pengajuan_id' => $pengajuan->id,
            'diubah_oleh' => $pegawai->id,
            'status_sebelumnya' => 'selesai',
            'status_baru' => 'diproses',
            'sub_status_sebelumnya' => null,
            'sub_status_baru' => 'identifikasi_klarifikasi',
            'catatan' => 'Keberatan dari pelapor: AC kembali tidak dingin setelah digunakan sekitar 30 menit.',
        ]);

        $response = $this->actingAs($pegawai)->get(route('pegawai.pengajuan.show', $pengajuan));

        $response->assertStatus(200);
        $response->assertSee('Detail pengaduan');
        $response->assertSee('Ditinjau ulang');
        $response->assertSee('Keberatan berhasil dikirim');
        $response->assertSee('Alasan keberatan');
        $response->assertSee('AC kembali tidak dingin setelah digunakan');
        $response->assertSee('Rincian aduan awal');
        $response->assertSee('Sarana &amp; prasarana', false);
        $response->assertSee('Progres pengaduan');
        $response->assertSee('Tinjau Ulang');
        $response->assertSee('Riwayat tanggapan');
        $response->assertSee('Anda akan menerima notifikasi tindak lanjut berikutnya.');
    }

    public function test_pegawai_can_download_own_pengajuan_pdf(): void
    {
        $pegawai = User::factory()->create(['role' => 'pegawai', 'is_aktif' => true]);
        $kategori = Kategori::create(['nama_kategori' => 'Fasilitas', 'aktif' => true]);

        $pengajuan = Pengajuan::create([
            'user_id' => $pegawai->id,
            'kategori_id' => $kategori->id,
            'deskripsi' => 'Pengaduan fasilitas kantor.',
            'metode_identitas' => 'identitas',
            'status' => 'menunggu',
        ]);

        $response = $this->actingAs($pegawai)->get(route('pegawai.pengajuan.pdf', $pengajuan));

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'application/pdf');
        $this->assertNotEmpty($response->getContent());
        $this->assertStringStartsWith('%PDF-', $response->getContent());
    }

    public function test_pegawai_cannot_download_other_pegawai_pengajuan_pdf(): void
    {
        $pegawai1 = User::factory()->create(['role' => 'pegawai', 'is_aktif' => true]);
        $pegawai2 = User::factory()->create(['role' => 'pegawai', 'is_aktif' => true]);
        $kategori = Kategori::create(['nama_kategori' => 'Fasilitas', 'aktif' => true]);

        $pengajuan = Pengajuan::create([
            'user_id' => $pegawai1->id,
            'kategori_id' => $kategori->id,
            'deskripsi' => 'Pengaduan pegawai 1.',
            'metode_identitas' => 'identitas',
            'status' => 'menunggu',
        ]);

        $response = $this->actingAs($pegawai2)->get(route('pegawai.pengajuan.pdf', $pengajuan));

        $response->assertStatus(403);
    }

    public function test_guest_cannot_download_pegawai_pengajuan_pdf(): void
    {
        $pegawai = User::factory()->create(['role' => 'pegawai', 'is_aktif' => true]);
        $kategori = Kategori::create(['nama_kategori' => 'Fasilitas', 'aktif' => true]);

        $pengajuan = Pengajuan::create([
            'user_id' => $pegawai->id,
            'kategori_id' => $kategori->id,
            'deskripsi' => 'Pengaduan pegawai.',
            'metode_identitas' => 'identitas',
            'status' => 'menunggu',
        ]);

        $response = $this->get(route('pegawai.pengajuan.pdf', $pengajuan));

        $response->assertRedirect(route('login'));
    }
}
