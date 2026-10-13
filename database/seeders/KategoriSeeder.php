<?php

namespace Database\Seeders;

use App\Models\Kategori;
use Illuminate\Database\Seeder;

class KategoriSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $kategoris = [
            [
                'nama_kategori' => 'Fasilitas & Sarana Prasarana',
                'aktif' => true,
            ],
            [
                'nama_kategori' => 'Pelayanan & Operasional',
                'aktif' => true,
            ],
            [
                'nama_kategori' => 'Teknologi Informasi & Jaringan',
                'aktif' => true,
            ],
            [
                'nama_kategori' => 'Disiplin & Etika Pegawai',
                'aktif' => true,
            ],
            [
                'nama_kategori' => 'Lain-lain',
                'aktif' => true,
            ],
        ];

        foreach ($kategoris as $kategori) {
            Kategori::firstOrCreate(
                ['nama_kategori' => $kategori['nama_kategori']],
                $kategori
            );
        }
    }
}
