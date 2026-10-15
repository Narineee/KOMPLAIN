<?php

namespace Tests\Feature;

use App\Models\Kategori;
use App\Models\Pengajuan;
use App\Models\TokenPengajuan;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CekPengajuanTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_can_view_cek_pengajuan_page(): void
    {
        $response = $this->get(route('cek-pengajuan.index'));

        $response->assertStatus(200);
    }

    public function test_public_can_verify_ticket_with_valid_token(): void
    {
        $kategori = Kategori::create(['nama' => 'Pelayanan', 'is_aktif' => true]);
        $plainToken = 'secret-token-1234567890abcdef';
        $tokenHash = hash('sha256', $plainToken);

        $pengajuan = Pengajuan::create([
            'user_id' => null,
            'kategori_id' => $kategori->id,
            'deskripsi' => 'Isi laporan pengaduan anonim.',
            'is_anonim' => true,
            'status' => 'diajukan',
        ]);

        TokenPengajuan::create([
            'pengajuan_id' => $pengajuan->id,
            'token_hash' => $tokenHash,
        ]);

        $response = $this->post(route('cek-pengajuan.check'), [
            'nomor_tiket' => '#'.$pengajuan->id,
            'token' => $plainToken,
        ]);

        $response->assertStatus(200);
        $response->assertViewHas('pengajuan', fn ($viewPengajuan) => $viewPengajuan->id === $pengajuan->id);
    }

    public function test_public_cannot_verify_with_invalid_ticket(): void
    {
        $response = $this->post(route('cek-pengajuan.check'), [
            'nomor_tiket' => '999999',
            'token' => 'random-token',
        ]);

        $response->assertSessionHasErrors('nomor_tiket');
    }

    public function test_public_cannot_verify_with_wrong_token(): void
    {
        $kategori = Kategori::create(['nama' => 'Pelayanan', 'is_aktif' => true]);

        $pengajuan = Pengajuan::create([
            'user_id' => null,
            'kategori_id' => $kategori->id,
            'deskripsi' => 'Isi laporan pengaduan anonim.',
            'is_anonim' => true,
            'status' => 'diajukan',
        ]);

        TokenPengajuan::create([
            'pengajuan_id' => $pengajuan->id,
            'token_hash' => hash('sha256', 'correct-token'),
        ]);

        $response = $this->post(route('cek-pengajuan.check'), [
            'nomor_tiket' => '#'.$pengajuan->id,
            'token' => 'wrong-token',
        ]);

        $response->assertSessionHasErrors('token');
    }
}
