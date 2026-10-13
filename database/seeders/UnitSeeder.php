<?php

namespace Database\Seeders;

use App\Models\Unit;
use Illuminate\Database\Seeder;

class UnitSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $units = [
            [
                'nama_unit' => 'Sekretariat',
                'keterangan' => 'Pengelolaan administrasi umum, keuangan, dan kepegawaian.',
            ],
            [
                'nama_unit' => 'Bidang Pelayanan & Pengaduan',
                'keterangan' => 'Pengelolaan operasional pelayanan dan penanganan aduan.',
            ],
            [
                'nama_unit' => 'Bidang Tata Kelola & Sistem Informasi',
                'keterangan' => 'Infrastruktur TI, perangkat lunak, dan tata kelola teknologi.',
            ],
            [
                'nama_unit' => 'Bidang Pengawasan & Akuntabilitas',
                'keterangan' => 'Audit internal, pengawasan kode etik, dan akuntabilitas kinerja.',
            ],
        ];

        foreach ($units as $unit) {
            Unit::firstOrCreate(['nama_unit' => $unit['nama_unit']], $unit);
        }
    }
}
