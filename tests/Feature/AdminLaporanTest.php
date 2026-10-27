<?php

namespace Tests\Feature;

use App\Models\Kategori;
use App\Models\Pengajuan;
use App\Models\RiwayatStatus;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
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
        $response->assertViewHasAll(['statistik', 'statistikTahun', 'statistikBulan', 'tahunAktif', 'bulanAktif', 'namaBulan', 'namaBulanTahun', 'laporanTerbaru', 'rentangTerbaru']);
    }

    public function test_admin_dashboard_recent_complaints_only_shows_reports_within_past_week(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'is_aktif' => true]);
        $kategori = Kategori::create(['nama_kategori' => 'Pelayanan', 'aktif' => true]);

        // Laporan lebih dari 7 hari lalu (misal 10 hari lalu)
        $lapLama = Pengajuan::create([
            'user_id' => null,
            'kategori_id' => $kategori->id,
            'judul' => 'Laporan 10 Hari Lalu',
            'isi_laporan' => 'Isi laporan lama.',
            'status' => 'diproses',
        ]);
        $lapLama->created_at = now()->subDays(10);
        $lapLama->saveQuietly();

        // Laporan dalam kurun 7 hari (3 hari lalu)
        $lap3Hari = Pengajuan::create([
            'user_id' => null,
            'kategori_id' => $kategori->id,
            'judul' => 'Laporan 3 Hari Lalu',
            'isi_laporan' => 'Isi laporan 3 hari lalu.',
            'status' => 'menunggu',
        ]);
        $lap3Hari->created_at = now()->subDays(3);
        $lap3Hari->saveQuietly();

        // Laporan hari ini
        $lapHariIni = Pengajuan::create([
            'user_id' => null,
            'kategori_id' => $kategori->id,
            'judul' => 'Laporan Hari Ini',
            'isi_laporan' => 'Isi laporan hari ini.',
            'status' => 'menunggu',
            'created_at' => now(),
        ]);

        $response = $this->actingAs($admin)->get(route('admin.dashboard'));

        $response->assertStatus(200);
        $laporanTerbaru = $response->viewData('laporanTerbaru');
        $rentangTerbaru = $response->viewData('rentangTerbaru');

        $this->assertEquals(7, $rentangTerbaru['hari']);
        $this->assertNotEmpty($rentangTerbaru['dari_teks']);
        $this->assertNotEmpty($rentangTerbaru['sampai_teks']);

        // Hanya memuat laporan dalam kurun 7 hari terakhir (3 hari lalu & hari ini)
        $this->assertTrue($laporanTerbaru->contains('id', $lap3Hari->id));
        $this->assertTrue($laporanTerbaru->contains('id', $lapHariIni->id));
        $this->assertFalse($laporanTerbaru->contains('id', $lapLama->id), 'Laporan 10 hari lalu tidak boleh masuk di laporan terbaru');
    }

    public function test_admin_dashboard_shows_yearly_and_monthly_realtime_summary(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'is_aktif' => true]);
        $kategori = Kategori::create(['nama_kategori' => 'Pelayanan', 'aktif' => true]);

        // 1 Laporan dari tahun lalu
        $lapTahunLalu = Pengajuan::create([
            'user_id' => null,
            'kategori_id' => $kategori->id,
            'judul' => 'Laporan Tahun Lalu',
            'isi_laporan' => 'Isi laporan tahun lalu.',
            'status' => 'selesai',
        ]);
        $lapTahunLalu->created_at = now()->subYear();
        $lapTahunLalu->saveQuietly();

        // 2 Laporan di bulan ini (realtime bulan berjalan)
        Pengajuan::create([
            'user_id' => null,
            'kategori_id' => $kategori->id,
            'judul' => 'Laporan Bulan Ini 1',
            'isi_laporan' => 'Isi laporan bulan ini 1.',
            'status' => 'menunggu',
            'created_at' => now(),
        ]);

        Pengajuan::create([
            'user_id' => null,
            'kategori_id' => $kategori->id,
            'judul' => 'Laporan Bulan Ini 2',
            'isi_laporan' => 'Isi laporan bulan ini 2.',
            'status' => 'selesai',
            'created_at' => now(),
        ]);

        $response = $this->actingAs($admin)->get(route('admin.dashboard'));

        $response->assertStatus(200);
        $response->assertViewHasAll(['statistik', 'statistikTahun', 'statistikBulan', 'tahunAktif', 'bulanAktif', 'namaBulan', 'namaBulanTahun']);

        $viewDataBulan = $response->viewData('statistikBulan');
        $this->assertEquals(2, $viewDataBulan['total']);
        $this->assertEquals(1, $viewDataBulan['diajukan']);
        $this->assertEquals(1, $viewDataBulan['selesai']);

        $viewDataTahun = $response->viewData('statistikTahun');
        $this->assertEquals(2, $viewDataTahun['total']);

        $viewDataAll = $response->viewData('statistik');
        $this->assertEquals(3, $viewDataAll['total']);

        // Uji otomatis reset ketika berganti bulan (simulasi pindah bulan)
        $this->travelTo(now()->addMonth()->startOfMonth());
        $responseBulanBaru = $this->actingAs($admin)->get(route('admin.dashboard'));
        $viewDataBulanBaru = $responseBulanBaru->viewData('statistikBulan');
        $this->assertEquals(0, $viewDataBulanBaru['total'], 'Statistik bulan berjalan harus reset ke 0 saat berganti bulan');
        $this->travelBack();
    }

    public function test_admin_can_access_rekap_page(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'is_aktif' => true]);

        $response = $this->actingAs($admin)->get(route('admin.rekap'));

        $response->assertStatus(200);
        $response->assertViewIs('admin.rekap');
        $response->assertViewHasAll(['laporan', 'ringkasan', 'kategoris', 'filter']);
    }

    public function test_admin_can_update_complaint_status_to_diproses_with_sub_processes(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'is_aktif' => true]);
        $kategori = Kategori::create(['nama_kategori' => 'Pelayanan', 'aktif' => true]);

        $pengajuan = Pengajuan::create([
            'user_id' => null,
            'kategori_id' => $kategori->id,
            'deskripsi' => 'Keluhan Sarana',
            'is_anonim' => true,
            'status' => 'menunggu',
        ]);

        // 1. Pindah ke tahap Identifikasi / Klarifikasi
        $response1 = $this->actingAs($admin)->patch(route('admin.laporan.status', $pengajuan), [
            'status' => 'diproses',
            'sub_status' => 'identifikasi_klarifikasi',
            'catatan' => 'Identifikasi akar permasalahan sarana.',
        ]);
        $response1->assertRedirect();
        $fresh = $pengajuan->fresh();
        $this->assertEquals('diproses', $fresh->status);
        $this->assertEquals('identifikasi_klarifikasi', $fresh->sub_status);
        $this->assertStringContainsString('Identifikasi / Klarifikasi', $fresh->status_label);

        // 2. Lanjut ke Rencana Aksi
        $response3 = $this->actingAs($admin)->patch(route('admin.laporan.status', $pengajuan), [
            'status' => 'diproses',
            'sub_status' => 'rencana_aksi',
            'catatan' => 'Penyusunan RAB perbaikan.',
        ]);
        $response3->assertRedirect();
        $fresh = $pengajuan->fresh();
        $this->assertEquals('rencana_aksi', $fresh->sub_status);

        // 3. Lanjut ke Aksi / Tindakan
        $response4 = $this->actingAs($admin)->patch(route('admin.laporan.status', $pengajuan), [
            'status' => 'diproses',
            'sub_status' => 'aksi',
            'catatan' => 'Perbaikan langsung di lokasi.',
        ]);
        $response4->assertRedirect();
        $fresh = $pengajuan->fresh();
        $this->assertEquals('aksi', $fresh->sub_status);

        $this->assertDatabaseHas('riwayat_status', [
            'pengajuan_id' => $pengajuan->id,
            'status_sebelumnya' => 'diproses',
            'status_baru' => 'diproses',
            'sub_status_sebelumnya' => 'rencana_aksi',
            'sub_status_baru' => 'aksi',
        ]);
    }

    public function test_admin_can_update_complaint_status_to_selesai(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'is_aktif' => true]);
        $kategori = Kategori::create(['nama_kategori' => 'Pelayanan', 'aktif' => true]);

        $pengajuan = Pengajuan::create([
            'user_id' => null,
            'kategori_id' => $kategori->id,
            'deskripsi' => 'Keluhan Di luar SOP',
            'is_anonim' => true,
            'status' => 'diproses',
            'sub_status' => 'identifikasi_klarifikasi',
        ]);

        $response = $this->actingAs($admin)->patch(route('admin.laporan.status', $pengajuan), [
            'status' => 'selesai',
            'catatan' => 'Setelah proses penanganan, aduan telah selesai.',
        ]);

        $response->assertRedirect();
        $fresh = $pengajuan->fresh();
        $this->assertEquals('selesai', $fresh->status);
        $this->assertNull($fresh->sub_status);
        $this->assertEquals('Selesai', $fresh->status_label);
        $this->assertNotNull($fresh->tanggal_selesai);

        $this->assertDatabaseHas('riwayat_status', [
            'pengajuan_id' => $pengajuan->id,
            'diubah_oleh' => $admin->id,
            'status_sebelumnya' => 'diproses',
            'status_baru' => 'selesai',
            'sub_status_sebelumnya' => 'identifikasi_klarifikasi',
            'sub_status_baru' => null,
        ]);
    }

    public function test_admin_can_update_complaint_status_to_selesai_and_records_tanggal_selesai(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'is_aktif' => true]);
        $kategori = Kategori::create(['nama_kategori' => 'Pelayanan', 'aktif' => true]);

        $pengajuan = Pengajuan::create([
            'user_id' => null,
            'kategori_id' => $kategori->id,
            'deskripsi' => 'Perbaikan Lampu',
            'is_anonim' => true,
            'status' => 'diproses',
            'sub_status' => 'aksi',
        ]);

        $response = $this->actingAs($admin)->patch(route('admin.laporan.status', $pengajuan), [
            'status' => 'selesai',
            'catatan' => 'Perbaikan lampu sudah selesai dilaksanakan.',
        ]);

        $response->assertRedirect();
        $fresh = $pengajuan->fresh();
        $this->assertEquals('selesai', $fresh->status);
        $this->assertNull($fresh->sub_status);
        $this->assertEquals('Selesai', $fresh->status_label);
        $this->assertNotNull($fresh->tanggal_selesai);
    }

    public function test_admin_can_update_reviewed_complaint_to_selesai_setelah_tinjau_ulang(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'is_aktif' => true]);
        $kategori = Kategori::create(['nama_kategori' => 'Pelayanan', 'aktif' => true]);

        $pengajuan = Pengajuan::create([
            'user_id' => null,
            'kategori_id' => $kategori->id,
            'deskripsi' => 'Pengaduan yang ditinjau ulang',
            'is_anonim' => true,
            'status' => 'diproses',
            'sub_status' => 'aksi',
            'konfirmasi_pelapor' => 'keberatan',
            'jumlah_keberatan' => 1,
        ]);

        $response = $this->actingAs($admin)->patch(route('admin.laporan.status', $pengajuan), [
            'status' => 'selesai',
            'catatan' => 'Tindak lanjut atas keberatan telah selesai diperiksa dan diperbaiki.',
        ]);

        $response->assertRedirect();
        $fresh = $pengajuan->fresh();
        $this->assertEquals('selesai', $fresh->status);
        $this->assertEquals('selesai_setelah_tinjau_ulang', $fresh->sub_status);
        $this->assertEquals('Selesai setelah tinjau ulang', $fresh->status_label);
        $this->assertNotNull($fresh->tanggal_selesai);

        $this->assertDatabaseHas('riwayat_status', [
            'pengajuan_id' => $pengajuan->id,
            'diubah_oleh' => $admin->id,
            'status_sebelumnya' => 'diproses',
            'status_baru' => 'selesai',
            'sub_status_sebelumnya' => 'aksi',
            'sub_status_baru' => 'selesai_setelah_tinjau_ulang',
        ]);
    }

    public function test_updating_complaint_status_notifies_user_if_not_anonim(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'is_aktif' => true]);
        $user = User::factory()->create(['role' => 'pegawai', 'is_aktif' => true]);
        $kategori = Kategori::create(['nama_kategori' => 'Pelayanan', 'aktif' => true]);

        $pengajuan = Pengajuan::create([
            'user_id' => $user->id,
            'kategori_id' => $kategori->id,
            'judul' => 'Pengaduan Beridentitas',
            'isi_laporan' => 'Detail masalah pegawai.',
            'metode_identitas' => 'identitas',
            'status' => 'menunggu',
        ]);

        $response = $this->actingAs($admin)->patch(route('admin.laporan.status', $pengajuan), [
            'status' => 'diproses',
            'catatan' => 'Sedang diverifikasi tim kami.',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('notifikasi', [
            'user_id' => $user->id,
            'pengajuan_id' => $pengajuan->id,
            'judul' => 'Status Laporan Diperbarui',
            'tipe' => 'status_update',
        ]);
    }

    public function test_admin_can_add_public_response_to_complaint_and_notifies_user(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'is_aktif' => true]);
        $user = User::factory()->create(['role' => 'pegawai', 'is_aktif' => true]);
        $kategori = Kategori::create(['nama_kategori' => 'Pelayanan', 'aktif' => true]);

        $pengajuan = Pengajuan::create([
            'user_id' => $user->id,
            'kategori_id' => $kategori->id,
            'judul' => 'Keluhan Sarana Publik',
            'isi_laporan' => 'Isi laporan sarana.',
            'metode_identitas' => 'identitas',
            'status' => 'diproses',
        ]);

        $response = $this->actingAs($admin)->post(route('admin.laporan.tanggapan', $pengajuan), [
            'pesan' => 'Petugas telah dikerahkan ke lokasi untuk perbaikan.',
            'is_internal' => '0',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('tanggapan', [
            'pengajuan_id' => $pengajuan->id,
            'admin_id' => $admin->id,
            'isi_tanggapan' => 'Petugas telah dikerahkan ke lokasi untuk perbaikan.',
            'is_internal' => false,
        ]);
        $this->assertDatabaseHas('notifikasi', [
            'user_id' => $user->id,
            'pengajuan_id' => $pengajuan->id,
            'judul' => 'Tanggapan Baru untuk Laporan Anda',
            'tipe' => 'tanggapan_baru',
        ]);
    }

    public function test_admin_can_add_internal_note_without_notifying_user(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'is_aktif' => true]);
        $user = User::factory()->create(['role' => 'pegawai', 'is_aktif' => true]);
        $kategori = Kategori::create(['nama_kategori' => 'Pelayanan', 'aktif' => true]);

        $pengajuan = Pengajuan::create([
            'user_id' => $user->id,
            'kategori_id' => $kategori->id,
            'judul' => 'Keluhan Internal Only',
            'isi_laporan' => 'Isi laporan sarana.',
            'metode_identitas' => 'identitas',
            'status' => 'diproses',
        ]);

        $response = $this->actingAs($admin)->post(route('admin.laporan.tanggapan', $pengajuan), [
            'pesan' => 'Catatan internal: koordinasikan dahulu dengan kabid sarpras.',
            'is_internal' => '1',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('tanggapan', [
            'pengajuan_id' => $pengajuan->id,
            'admin_id' => $admin->id,
            'isi_tanggapan' => 'Catatan internal: koordinasikan dahulu dengan kabid sarpras.',
            'is_internal' => true,
        ]);
        $this->assertDatabaseMissing('notifikasi', [
            'pengajuan_id' => $pengajuan->id,
            'tipe' => 'tanggapan_baru',
        ]);
    }

    public function test_admin_can_filter_complaints_by_date_range(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'is_aktif' => true]);
        $kategori = Kategori::create(['nama_kategori' => 'Pelayanan', 'aktif' => true]);

        // Laporan A: 2026-10-01
        $lapA = Pengajuan::create([
            'user_id' => null,
            'kategori_id' => $kategori->id,
            'judul' => 'Laporan Awal Bulan',
            'isi_laporan' => 'Isi laporan awal bulan.',
            'status' => 'menunggu',
        ]);
        $lapA->created_at = '2026-10-01 10:00:00';
        $lapA->saveQuietly();

        // Laporan B: 2026-10-10
        $lapB = Pengajuan::create([
            'user_id' => null,
            'kategori_id' => $kategori->id,
            'judul' => 'Laporan Pertengahan Bulan',
            'isi_laporan' => 'Isi laporan tengah bulan.',
            'status' => 'diproses',
        ]);
        $lapB->created_at = '2026-10-10 14:00:00';
        $lapB->saveQuietly();

        // Laporan C: 2026-10-20
        $lapC = Pengajuan::create([
            'user_id' => null,
            'kategori_id' => $kategori->id,
            'judul' => 'Laporan Akhir Bulan',
            'isi_laporan' => 'Isi laporan akhir bulan.',
            'status' => 'selesai',
        ]);
        $lapC->created_at = '2026-10-20 09:00:00';
        $lapC->saveQuietly();

        // Filter rentang: 2026-10-05 s/d 2026-10-15
        $response = $this->actingAs($admin)->get(route('admin.laporan.index', [
            'dari_tanggal' => '2026-10-05',
            'sampai_tanggal' => '2026-10-15',
        ]));

        $response->assertStatus(200);
        $pengajuans = $response->viewData('pengajuans');

        $this->assertTrue($pengajuans->contains('id', $lapB->id));
        $this->assertFalse($pengajuans->contains('id', $lapA->id));
        $this->assertFalse($pengajuans->contains('id', $lapC->id));

        // Filter hanya dari_tanggal: >= 2026-10-15
        $responseDari = $this->actingAs($admin)->get(route('admin.laporan.index', [
            'dari_tanggal' => '2026-10-15',
        ]));
        $pengajuansDari = $responseDari->viewData('pengajuans');
        $this->assertTrue($pengajuansDari->contains('id', $lapC->id));
        $this->assertFalse($pengajuansDari->contains('id', $lapA->id));
        $this->assertFalse($pengajuansDari->contains('id', $lapB->id));

        // Filter hanya sampai_tanggal: <= 2026-10-05
        $responseSampai = $this->actingAs($admin)->get(route('admin.laporan.index', [
            'sampai_tanggal' => '2026-10-05',
        ]));
        $pengajuansSampai = $responseSampai->viewData('pengajuans');
        $this->assertTrue($pengajuansSampai->contains('id', $lapA->id));
        $this->assertFalse($pengajuansSampai->contains('id', $lapB->id));
        $this->assertFalse($pengajuansSampai->contains('id', $lapC->id));
    }

    public function test_admin_views_display_verifikasi_and_uraian_max_50_chars(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'is_aktif' => true]);
        $pegawai = User::factory()->create(['role' => 'pegawai', 'nama' => 'Budi Santoso', 'is_aktif' => true]);
        $kategori = Kategori::create(['nama_kategori' => 'Fasilitas', 'aktif' => true]);

        $longDescription = 'Plafon di ruangan lantai 2 bocor sangat parah hingga membasahi berkas arsip penting dan mengganggu rapat dinas harian.';
        $expectedTruncated = Str::limit($longDescription, 50);

        $pengajuan = Pengajuan::create([
            'user_id' => $pegawai->id,
            'kategori_id' => $kategori->id,
            'deskripsi' => $longDescription,
            'status' => 'menunggu',
            'metode_identitas' => 'identitas',
        ]);

        // 1. Dashboard
        $responseDashboard = $this->actingAs($admin)->get(route('admin.dashboard'));
        $responseDashboard->assertStatus(200);
        $responseDashboard->assertSee('Verifikasi');
        $responseDashboard->assertSee('Budi Santoso');
        $responseDashboard->assertSee($expectedTruncated);

        // 2. Laporan Index
        $responseIndex = $this->actingAs($admin)->get(route('admin.laporan.index'));
        $responseIndex->assertStatus(200);
        $responseIndex->assertSee('Verifikasi');
        $responseIndex->assertSee('Budi Santoso');
        $responseIndex->assertSee($expectedTruncated);

        // 3. Rekap
        $responseRekap = $this->actingAs($admin)->get(route('admin.rekap'));
        $responseRekap->assertStatus(200);
        $responseRekap->assertSee('Verifikasi');
        $responseRekap->assertSee('Budi Santoso');
        $responseRekap->assertSee($expectedTruncated);

        // 4. Update status with 'verifikasi'
        $responseUpdate = $this->actingAs($admin)->patch(route('admin.laporan.status', $pengajuan), [
            'status' => 'verifikasi',
            'catatan' => 'Verifikasi ulang berkas laporan.',
        ]);
        $responseUpdate->assertRedirect();
        $this->assertDatabaseHas('pengajuan', [
            'id' => $pengajuan->id,
            'status' => 'verifikasi',
        ]);
    }

    public function test_admin_cannot_reveal_anonim_without_password(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'password' => bcrypt('secret123'), 'is_aktif' => true]);
        $pegawai = User::factory()->create(['role' => 'pegawai', 'nama' => 'Rahasia Pelapor', 'is_aktif' => true]);
        $kategori = Kategori::create(['nama_kategori' => 'Pelayanan', 'aktif' => true]);

        $pengajuan = Pengajuan::create([
            'user_id' => $pegawai->id,
            'kategori_id' => $kategori->id,
            'deskripsi' => 'Pengaduan sensitif secara anonim.',
            'is_anonim' => true,
            'status' => 'menunggu',
        ]);

        $response = $this->actingAs($admin)->postJson(route('admin.laporan.reveal-anonim', $pengajuan), []);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['password']);
        $this->assertFalse(session()->has('unlocked_anonim_'.$pengajuan->id));
    }

    public function test_admin_cannot_reveal_anonim_with_wrong_password(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'password' => bcrypt('secret123'), 'is_aktif' => true]);
        $pegawai = User::factory()->create(['role' => 'pegawai', 'nama' => 'Rahasia Pelapor', 'is_aktif' => true]);
        $kategori = Kategori::create(['nama_kategori' => 'Pelayanan', 'aktif' => true]);

        $pengajuan = Pengajuan::create([
            'user_id' => $pegawai->id,
            'kategori_id' => $kategori->id,
            'deskripsi' => 'Pengaduan sensitif secara anonim.',
            'is_anonim' => true,
            'status' => 'menunggu',
        ]);

        $response = $this->actingAs($admin)->postJson(route('admin.laporan.reveal-anonim', $pengajuan), [
            'password' => 'passwordsalah123',
        ]);

        $response->assertStatus(422);
        $response->assertJson([
            'success' => false,
            'message' => 'Password atau kunci otorisasi salah. Akses ditolak.',
        ]);
        $this->assertFalse(session()->has('unlocked_anonim_'.$pengajuan->id));
    }

    public function test_admin_can_reveal_anonim_with_admin_account_password(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'password' => bcrypt('adminpass123'), 'is_aktif' => true]);
        $pegawai = User::factory()->create(['role' => 'pegawai', 'nama' => 'Ahmad Pelapor', 'is_aktif' => true]);
        $kategori = Kategori::create(['nama_kategori' => 'Pelayanan', 'aktif' => true]);

        $pengajuan = Pengajuan::create([
            'user_id' => $pegawai->id,
            'kategori_id' => $kategori->id,
            'deskripsi' => 'Pengaduan sensitif secara anonim.',
            'is_anonim' => true,
            'status' => 'menunggu',
        ]);

        $response = $this->actingAs($admin)->postJson(route('admin.laporan.reveal-anonim', $pengajuan), [
            'password' => 'adminpass123',
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'message' => 'Kunci identitas pelapor berhasil dibuka.',
            'data' => [
                'nama' => 'Ahmad Pelapor',
            ],
        ]);
        $this->assertTrue(session('unlocked_anonim_'.$pengajuan->id));
    }

    public function test_admin_can_reveal_anonim_with_master_key(): void
    {
        config(['app.admin_anonim_key' => 'masterkey999']);

        $admin = User::factory()->create(['role' => 'admin', 'password' => bcrypt('differentpass'), 'is_aktif' => true]);
        $pegawai = User::factory()->create(['role' => 'pegawai', 'nama' => 'Siti Pelapor', 'is_aktif' => true]);
        $kategori = Kategori::create(['nama_kategori' => 'Pelayanan', 'aktif' => true]);

        $pengajuan = Pengajuan::create([
            'user_id' => $pegawai->id,
            'kategori_id' => $kategori->id,
            'deskripsi' => 'Pengaduan sensitif dari Siti.',
            'is_anonim' => true,
            'status' => 'menunggu',
        ]);

        $response = $this->actingAs($admin)->postJson(route('admin.laporan.reveal-anonim', $pengajuan), [
            'password' => 'masterkey999',
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'data' => [
                'nama' => 'Siti Pelapor',
            ],
        ]);
    }

    public function test_admin_can_lock_anonim_again(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'is_aktif' => true]);
        $kategori = Kategori::create(['nama_kategori' => 'Pelayanan', 'aktif' => true]);

        $pengajuan = Pengajuan::create([
            'user_id' => null,
            'kategori_id' => $kategori->id,
            'deskripsi' => 'Pengaduan anonim.',
            'is_anonim' => true,
            'status' => 'menunggu',
        ]);

        session(['unlocked_anonim_'.$pengajuan->id => true]);

        $response = $this->actingAs($admin)->postJson(route('admin.laporan.lock-anonim', $pengajuan));

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);
        $this->assertFalse(session()->has('unlocked_anonim_'.$pengajuan->id));
    }

    public function test_pegawai_cannot_access_reveal_anonim_endpoint(): void
    {
        $pegawai = User::factory()->create(['role' => 'pegawai', 'is_aktif' => true]);
        $kategori = Kategori::create(['nama_kategori' => 'Pelayanan', 'aktif' => true]);

        $pengajuan = Pengajuan::create([
            'user_id' => $pegawai->id,
            'kategori_id' => $kategori->id,
            'deskripsi' => 'Pengaduan anonim.',
            'is_anonim' => true,
            'status' => 'menunggu',
        ]);

        $response = $this->actingAs($pegawai)->postJson(route('admin.laporan.reveal-anonim', $pengajuan), [
            'password' => 'admin123',
        ]);

        $response->assertStatus(403);
    }

    public function test_guest_cannot_access_reveal_anonim_endpoint(): void
    {
        $kategori = Kategori::create(['nama_kategori' => 'Pelayanan', 'aktif' => true]);

        $pengajuan = Pengajuan::create([
            'user_id' => null,
            'kategori_id' => $kategori->id,
            'deskripsi' => 'Pengaduan anonim.',
            'is_anonim' => true,
            'status' => 'menunggu',
        ]);

        $response = $this->post(route('admin.laporan.reveal-anonim', $pengajuan), [
            'password' => 'admin123',
        ]);

        $response->assertRedirect(route('login'));
    }

    public function test_admin_laporan_show_view_renders_unlocked_state_when_session_unlocked(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'is_aktif' => true]);
        $pegawai = User::factory()->create(['role' => 'pegawai', 'nama' => 'Bambang Anonim', 'is_aktif' => true]);
        $kategori = Kategori::create(['nama_kategori' => 'Pelayanan', 'aktif' => true]);

        $pengajuan = Pengajuan::create([
            'user_id' => $pegawai->id,
            'kategori_id' => $kategori->id,
            'deskripsi' => 'Uraian pengaduan anonim oleh bambang.',
            'is_anonim' => true,
            'status' => 'menunggu',
        ]);

        // Belum dibuka: ada tombol Buka Kunci Identitas Asli
        $responseLocked = $this->actingAs($admin)->get(route('admin.laporan.show', $pengajuan));
        $responseLocked->assertStatus(200);
        $responseLocked->assertSee('Buka Kunci Identitas Asli');
        $responseLocked->assertSee('Laporan Anonim');

        // Setelah dibuka di session:
        session(['unlocked_anonim_'.$pengajuan->id => true]);

        $responseUnlocked = $this->actingAs($admin)->get(route('admin.laporan.show', $pengajuan));
        $responseUnlocked->assertStatus(200);
        $responseUnlocked->assertSee('Bambang Anonim');
        $responseUnlocked->assertSee('Kunci Identitas Terbuka');
        $responseUnlocked->assertSee('Kunci Lagi');
    }

    public function test_admin_can_download_rekap_pdf(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'is_aktif' => true]);
        $kategori = Kategori::create(['nama_kategori' => 'Pelayanan', 'aktif' => true]);

        Pengajuan::create([
            'user_id' => null,
            'kategori_id' => $kategori->id,
            'deskripsi' => 'Pengaduan untuk rekap PDF.',
            'status' => 'menunggu',
        ]);

        $response = $this->actingAs($admin)->get(route('admin.rekap.pdf'));

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'application/pdf');
        $this->assertNotEmpty($response->getContent());
        $this->assertStringStartsWith('%PDF-', $response->getContent());
    }

    public function test_admin_can_download_rekap_pdf_with_filter(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'is_aktif' => true]);
        $kategori = Kategori::create(['nama_kategori' => 'Pelayanan', 'aktif' => true]);

        Pengajuan::create([
            'user_id' => null,
            'kategori_id' => $kategori->id,
            'deskripsi' => 'Pengaduan filter status.',
            'status' => 'diproses',
            'sub_status' => 'identifikasi',
        ]);

        $response = $this->actingAs($admin)->get(route('admin.rekap.pdf', [
            'status' => 'diproses',
            'kategori_id' => $kategori->id,
        ]));

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'application/pdf');
        $this->assertStringStartsWith('%PDF-', $response->getContent());
    }

    public function test_pegawai_cannot_download_admin_rekap_pdf(): void
    {
        $pegawai = User::factory()->create(['role' => 'pegawai', 'is_aktif' => true]);

        $response = $this->actingAs($pegawai)->get(route('admin.rekap.pdf'));

        $response->assertStatus(403);
    }

    public function test_admin_can_download_laporan_detail_pdf(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'is_aktif' => true]);
        $kategori = Kategori::create(['nama_kategori' => 'Pelayanan', 'aktif' => true]);

        $pengajuan = Pengajuan::create([
            'user_id' => null,
            'kategori_id' => $kategori->id,
            'deskripsi' => 'Rincian pengaduan untuk dicetak PDF.',
            'status' => 'menunggu',
        ]);

        $response = $this->actingAs($admin)->get(route('admin.laporan.pdf', $pengajuan));

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'application/pdf');
        $this->assertStringStartsWith('%PDF-', $response->getContent());
    }

    public function test_new_pengajuan_is_belum_verifikasi_and_automatically_becomes_verifikasi_when_admin_opens_detail(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'is_aktif' => true]);
        $pegawai = User::factory()->create(['role' => 'pegawai', 'is_aktif' => true]);
        $kategori = Kategori::create(['nama_kategori' => 'Fasilitas', 'aktif' => true]);

        $pengajuan = Pengajuan::create([
            'user_id' => $pegawai->id,
            'kategori_id' => $kategori->id,
            'deskripsi' => 'Pendingin ruangan mati total di ruangan kerja.',
            'status' => 'belum_verifikasi',
            'metode_identitas' => 'identitas',
        ]);

        $this->assertSame('belum_verifikasi', $pengajuan->status);
        $this->assertSame('Belum Diverifikasi', $pengajuan->status_label);

        // Sebelum dibuka admin: di dashboard dan daftar laporan tertulis "Belum Diverifikasi"
        $respDashboard = $this->actingAs($admin)->get(route('admin.dashboard'));
        $respDashboard->assertSee('Belum Diverifikasi');

        $respIndex = $this->actingAs($admin)->get(route('admin.laporan.index'));
        $respIndex->assertSee('Belum Diverifikasi');

        // Saat admin membuka halaman detail laporan: otomatis diverifikasi
        $respShow = $this->actingAs($admin)->get(route('admin.laporan.show', $pengajuan));
        $respShow->assertStatus(200);
        $respShow->assertSee('Status: Verifikasi');

        $pengajuan->refresh();
        $this->assertSame('verifikasi', $pengajuan->status);
        $this->assertSame('Verifikasi', $pengajuan->status_label);

        // Tercatat di riwayat status
        $this->assertDatabaseHas('riwayat_status', [
            'pengajuan_id' => $pengajuan->id,
            'status_sebelumnya' => 'belum_verifikasi',
            'status_baru' => 'verifikasi',
            'catatan' => 'Laporan telah dibuka dan diverifikasi oleh admin.',
        ]);

        // Membuka kedua kali tidak menduplikasi riwayat status
        $countRiwayat = RiwayatStatus::where('pengajuan_id', $pengajuan->id)->count();
        $this->actingAs($admin)->get(route('admin.laporan.show', $pengajuan));
        $this->assertSame($countRiwayat, RiwayatStatus::where('pengajuan_id', $pengajuan->id)->count());
    }

    public function test_admin_can_mark_laporan_back_to_belum_verifikasi_and_verify_again(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'is_aktif' => true]);
        $pegawai = User::factory()->create(['role' => 'pegawai', 'is_aktif' => true]);
        $kategori = Kategori::create(['nama_kategori' => 'Pelayanan', 'aktif' => true]);

        $pengajuan = Pengajuan::create([
            'user_id' => $pegawai->id,
            'kategori_id' => $kategori->id,
            'deskripsi' => 'Pengaduan yang sudah sempat diverifikasi.',
            'status' => 'verifikasi',
        ]);

        // 1. Admin menandai kembali sebagai Belum Diverifikasi
        $respTandai = $this->actingAs($admin)->patch(route('admin.laporan.tandai-belum-verifikasi', $pengajuan));
        $respTandai->assertRedirect(route('admin.laporan.show', ['pengajuan' => $pengajuan, 'keep_unverified' => 1]));

        $pengajuan->refresh();
        $this->assertSame('belum_verifikasi', $pengajuan->status);

        $this->assertDatabaseHas('riwayat_status', [
            'pengajuan_id' => $pengajuan->id,
            'status_sebelumnya' => 'verifikasi',
            'status_baru' => 'belum_verifikasi',
        ]);

        // 2. Admin memverifikasi kembali secara manual
        $respVerif = $this->actingAs($admin)->patch(route('admin.laporan.verifikasi', $pengajuan));
        $respVerif->assertRedirect();

        $pengajuan->refresh();
        $this->assertSame('verifikasi', $pengajuan->status);
    }

    public function test_admin_can_filter_by_belum_verifikasi(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'is_aktif' => true]);
        $kategori = Kategori::create(['nama_kategori' => 'Fasilitas', 'aktif' => true]);

        $lapBelum = Pengajuan::create([
            'user_id' => null,
            'kategori_id' => $kategori->id,
            'deskripsi' => 'Laporan khusus status belum verifikasi unik123.',
            'status' => 'belum_verifikasi',
        ]);

        $lapSelesai = Pengajuan::create([
            'user_id' => null,
            'kategori_id' => $kategori->id,
            'deskripsi' => 'Laporan khusus status selesai unik456.',
            'status' => 'selesai',
        ]);

        $response = $this->actingAs($admin)->get(route('admin.laporan.index', ['status' => 'belum_verifikasi']));
        $response->assertStatus(200);
        $response->assertSee('unik123');
        $response->assertDontSee('unik456');
    }
}
