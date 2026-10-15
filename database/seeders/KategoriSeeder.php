<?php

namespace Database\Seeders;

use App\Models\Kategori;
use App\Models\Pengajuan;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

class KategoriSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $klasifikasis = [
            [
                'nama_kategori' => 'Kode Etik Pegawai',
                'keterangan' => 'Pengaduan terkait pelanggaran disiplin kerja, integritas, kepatuhan kode etik aparatur, profesionalisme, atau penyalahgunaan wewenang pegawai.',
                'aktif' => true,
                'children' => [],
            ],
            [
                'nama_kategori' => 'Kebersihan',
                'keterangan' => 'Pengaduan terkait kebersihan lingkungan kantor, sanitasi ruang kerja, pembersihan area umum, pengelolaan tempat sampah, dan kenyamanan lingkungan kerja.',
                'aktif' => true,
                'children' => [],
            ],
            [
                'nama_kategori' => 'Keamanan',
                'keterangan' => 'Pengaduan terkait keamanan dan ketertiban lingkungan kantor, pos satpam/penjagaan, kehilangan aset dinas, pengawasan akses, atau potensi gangguan keselamatan.',
                'aktif' => true,
                'children' => [],
            ],
            [
                'nama_kategori' => 'Kendaraan',
                'keterangan' => 'Pengaduan terkait kondisi fisik, servis berkala, kelayakan mesin, maupun operasional kendaraan dinas/kantor (R2, R3, dan R4).',
                'aktif' => true,
                'children' => [
                    [
                        'nama_kategori' => 'R2',
                        'keterangan' => 'Pengaduan kendaraan dinas roda dua (sepeda motor operasional pegawai).',
                        'aktif' => true,
                    ],
                    [
                        'nama_kategori' => 'R3',
                        'keterangan' => 'Pengaduan kendaraan dinas roda tiga (motor roda tiga/tossa/viar operasional angkut/kebersihan).',
                        'aktif' => true,
                    ],
                    [
                        'nama_kategori' => 'R4',
                        'keterangan' => 'Pengaduan kendaraan dinas roda empat (mobil dinas, minibus, atau armada roda empat dinas).',
                        'aktif' => true,
                    ],
                ],
            ],
            [
                'nama_kategori' => 'Taman/Halaman',
                'keterangan' => 'Pengaduan terkait perawatan tanaman dan taman, pemotongan rumput/pohon, penataan lansekap hijau, drainase terbuka, serta kebersihan halaman kantor.',
                'aktif' => true,
                'children' => [],
            ],
            [
                'nama_kategori' => 'Bangunan/Gedung',
                'keterangan' => 'Pengaduan terkait kondisi fisik dan struktur bangunan/gedung kantor seperti atap, dinding, plafon, pos penjagaan, dan toilet.',
                'aktif' => true,
                'children' => [
                    [
                        'nama_kategori' => 'Atap',
                        'keterangan' => 'Pengaduan kerusakan genteng, atap bocor, seng/asbes rusak, talang air, atau rangka atap rapuh.',
                        'aktif' => true,
                    ],
                    [
                        'nama_kategori' => 'Dinding',
                        'keterangan' => 'Pengaduan keretakan tembok, rembesan air pada dinding, cat mengelupas, atau kusen pintu dan jendela.',
                        'aktif' => true,
                    ],
                    [
                        'nama_kategori' => 'Plafon',
                        'keterangan' => 'Pengaduan plafon jebol, berjamur, bekas rembesan air hujan, atau kerangka plafon lapuk.',
                        'aktif' => true,
                    ],
                    [
                        'nama_kategori' => 'Pos',
                        'keterangan' => 'Pengaduan fasilitas pos satpam/keamanan, pos jaga gerbang, atau pos parkir kantor.',
                        'aktif' => true,
                    ],
                    [
                        'nama_kategori' => 'Toilet',
                        'keterangan' => 'Pengaduan kran air rusak, kloset, wastafel mampet, pintu toilet, atau sanitasi bau.',
                        'aktif' => true,
                    ],
                ],
            ],
            [
                'nama_kategori' => 'Sarana/Pra Sarana',
                'keterangan' => 'Pengaduan terkait perlengkapan dan sarana prasarana penunjang aktivitas kerja kantor seperti air, penerangan lampu, lemari arsip, pendingin ruangan (AC), kursi, dan meja.',
                'aktif' => true,
                'children' => [
                    [
                        'nama_kategori' => 'Air',
                        'keterangan' => 'Pengaduan suplai air bersih, pipa bocor, pompa air macet, kran air, atau tandon penampungan air.',
                        'aktif' => true,
                    ],
                    [
                        'nama_kategori' => 'Lampu',
                        'keterangan' => 'Pengaduan lampu penerangan ruangan padam, lampu koridor, saklar listrik rusak, atau instalasi kelistrikan.',
                        'aktif' => true,
                    ],
                    [
                        'nama_kategori' => 'Lemari',
                        'keterangan' => 'Pengaduan kerusakan lemari berkas/arsip dinas, engsel patah, kunci rusak, atau loker pegawai.',
                        'aktif' => true,
                    ],
                    [
                        'nama_kategori' => 'AC',
                        'keterangan' => 'Pengaduan pendingin ruangan (AC) tidak dingin, bocor air, bersuara bising, remote rusak, atau AC mati total.',
                        'aktif' => true,
                    ],
                    [
                        'nama_kategori' => 'Kursi',
                        'keterangan' => 'Pengaduan kerusakan kursi kerja pegawai, hidrolik patah, roda lepas, atau kursi rapat/tamu.',
                        'aktif' => true,
                    ],
                    [
                        'nama_kategori' => 'Meja',
                        'keterangan' => 'Pengaduan kerusakan meja kerja, laci macet, permukaan meja rusak/retak, atau meja layanan.',
                        'aktif' => true,
                    ],
                    [
                        'nama_kategori' => 'Lainnya',
                        'keterangan' => 'Pengaduan fasilitas sarana dan prasarana penunjang kantor lainnya yang belum tercantum.',
                        'aktif' => true,
                    ],
                ],
            ],
            [
                'nama_kategori' => 'Lainnya',
                'keterangan' => 'Pengaduan untuk permasalahan atau aspek lain yang belum tercakup dalam klasifikasi pengaduan yang tersedia di atas.',
                'aktif' => true,
                'children' => [],
            ],
        ];

        Schema::disableForeignKeyConstraints();
        Kategori::truncate();
        Schema::enableForeignKeyConstraints();

        $firstCreatedId = null;

        foreach ($klasifikasis as $item) {
            $parent = Kategori::create([
                'parent_id' => null,
                'nama_kategori' => $item['nama_kategori'],
                'keterangan' => $item['keterangan'],
                'aktif' => $item['aktif'],
            ]);

            if ($firstCreatedId === null) {
                $firstCreatedId = $parent->id;
            }

            if (! empty($item['children'])) {
                foreach ($item['children'] as $child) {
                    Kategori::create([
                        'parent_id' => $parent->id,
                        'nama_kategori' => $child['nama_kategori'],
                        'keterangan' => $child['keterangan'],
                        'aktif' => $child['aktif'],
                    ]);
                }
            }
        }

        // Remap pengajuan existing jika kategori_id-nya sudah tidak ada di tabel kategori
        if ($firstCreatedId) {
            $validKategoriIds = Kategori::pluck('id')->all();
            Pengajuan::whereNotIn('kategori_id', $validKategoriIds)->update(['kategori_id' => $firstCreatedId]);
        }
    }
}
