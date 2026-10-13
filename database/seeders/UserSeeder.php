<?php

namespace Database\Seeders;

use App\Models\Unit;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $sekretariatId = Unit::where('nama_unit', 'Sekretariat')->value('id');
        $pelayananId = Unit::where('nama_unit', 'Bidang Pelayanan & Pengaduan')->value('id');
        $tiId = Unit::where('nama_unit', 'Bidang Tata Kelola & Sistem Informasi')->value('id');

        $users = [
            [
                'nip' => '198001012005011001',
                'nama' => 'Administrator Sistem',
                'email' => 'admin@komplain.test',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'unit_id' => $sekretariatId,
                'status_akun' => 'aktif',
            ],
            [
                'nip' => '199001012015011002',
                'nama' => 'User Pegawai',
                'email' => 'user@komplain.test',
                'password' => Hash::make('password'),
                'role' => 'pegawai',
                'unit_id' => $pelayananId,
                'status_akun' => 'aktif',
            ],
            [
                'nip' => '199001012015011003',
                'nama' => 'Pegawai Pengadu',
                'email' => 'pegawai@komplain.test',
                'password' => Hash::make('password'),
                'role' => 'pegawai',
                'unit_id' => $tiId,
                'status_akun' => 'aktif',
            ],
        ];

        foreach ($users as $userData) {
            User::updateOrCreate(
                ['email' => $userData['email']],
                $userData
            );
        }
    }
}
