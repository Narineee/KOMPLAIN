1. Gambaran umum sistem

KOMPLAIN adalah aplikasi web internal yang digunakan pegawai untuk menyampaikan laporan atau keluhan terkait masalah pelayanan dan urusan internal dinas. Administrator bertugas mengelola laporan, memberikan tanggapan, dan memperbarui status penanganannya.

Sistem memiliki dua role:
Pegawai (pegawai) — pengguna internal yang login dan mengajukan laporan.
Administrator (admin) — pengguna yang mengelola dan menindaklanjuti laporan.

Pegawai dapat memilih dua mode pengajuan:
Dengan identitas: laporan terhubung dengan akun pegawai.
Anonim: laporan tidak menyimpan hubungan langsung dengan akun pelapor dan diakses kembali menggunakan nomor tiket serta token rahasia.

2. Alur lengkap sistem
Halaman utama KOMPLAIN
Dua pilihan: Login Pegawai dan Cek Pengajuan
Login Pegawai
Untuk mengajukan laporan
Cek Pengajuan
Tiket + token, tanpa login
Setelah login
Pemeriksaan role
Laravel membaca users.role
Role pegawai
Dashboard pegawai → Buat laporan → Pilih mode identitas → Simpan laporan → Terima tiket dan token.

Role admin
Dashboard admin → Daftar laporan → Detail laporan → Ubah status → Beri tanggapan.
Pengecekan laporan
Nomor tiket dan token diverifikasi sebelum status dan tanggapan yang diizinkan ditampilkan.

A. Alur pegawai
Pegawai membuka aplikasi KOMPLAIN.
Pegawai login menggunakan akun yang telah terdaftar.
Sistem memeriksa email/NIP, password, status akun, dan role.
Jika role pegawai, pengguna diarahkan ke dashboard pegawai.
Pegawai memilih menu Buat Pengajuan.
Pegawai mengisi kategori, judul, isi laporan, dan lampiran jika diperlukan.
Pegawai memilih mode pengajuan:
Dengan identitas.
Anonim.
Sistem memvalidasi data, membuat nomor tiket unik, dan membuat token rahasia.
Sistem menyimpan pengajuan dan hash token.
Sistem menampilkan nomor tiket dan token asli kepada pelapor agar disimpan.
Pegawai dapat keluar dari akun. Untuk mengecek laporan berikutnya, ia membuka halaman Cek Pengajuan dan memasukkan tiket beserta token.

B. Alur pengajuan dengan identitas
Jika pegawai memilih menggunakan identitas:
is_anonim = FALSE
user_id diisi dengan ID pegawai yang sedang login.
Admin dapat mengetahui identitas pelapor sesuai hak akses.
Laporan dapat ditampilkan dalam riwayat pengajuan pegawai setelah autentikasi dan otorisasi diterapkan.

C. Alur pengajuan anonim
Jika pegawai memilih anonim:
is_anonim = TRUE
user_id = NULL
Nomor tiket dibuat secara unik.
Token rahasia dibuat secara acak dan hash-nya disimpan.
Admin melihat laporan tanpa identitas akun pelapor.
Pelapor menggunakan nomor tiket dan token untuk memeriksa status dan tanggapan.
Penting: login digunakan untuk membatasi siapa yang boleh membuat laporan, tetapi sistem tidak boleh menyimpan hubungan akun pegawai dengan ID laporan anonim. Untuk mendekati anonimitas absolut, log aplikasi, audit log, log server, dan metadata lainnya juga harus dirancang agar tidak menghubungkan kedua hal tersebut. Jangan menjanjikan anonimitas absolut sebelum aspek ini diuji.

3. Rancangan database yang digunakan
Nama database: komplain
Kita menggunakan satu tabel users untuk login dan role, tanpa tabel login admin terpisah. Status laporan menggunakan ENUM langsung di tabel pengajuan, jadi tabel status_pengajuan tidak diperlukan untuk versi awal.

5. Daftar halaman yang perlu dibuat
HalamanAkses
Landing page Publik
Login Publik
Dashboard pegawai
Pegawai Buat pengajuan Pegawai
Detail pengajuan teridentifikasi
Pegawai pemilik laporan yang terautentikasi




Cek pengajuan dengan tiket dan token

	

Publik




Dashboard admin

	

Admin




Daftar dan detail laporan

	

Admin




Kelola kategori

	

Admin




Kelola akun pegawai

	

Admin




Tanggapan dan perubahan status

	

Admin




Rekap laporan

	

Admin

6. Rekomendasi struktur Laravel

Agar implementasinya teratur, susun komponen berikut:

Models: User, Unit, Kategori, Pengajuan, TokenPengajuan, Tanggapan, Lampiran, RiwayatStatus, dan Notifikasi.

Controllers: AuthController, PengajuanController, CekPengajuanController, AdminDashboardController, KategoriController, dan TanggapanController.

Middleware: autentikasi dan pemeriksaan role admin/pegawai.

Migrations: satu migration untuk setiap tabel, ditambah seeder untuk kategori awal dan akun admin pertama.

Views: halaman login, dashboard pegawai, formulir pengajuan, cek tiket, dan dashboard admin.

7. Kriteria bahwa sistem sudah sesuai
Admin dan pegawai login menggunakan tabel users.
Role membatasi akses halaman dan tindakan di server.
Hanya pegawai aktif yang dapat mengajukan laporan.
Status disimpan langsung dalam pengajuan menggunakan ENUM.
Laporan anonim menyimpan user_id = NULL.
Setiap laporan memiliki nomor tiket dan token rahasia.
Token asli tidak disimpan di database.
Pengecekan tiket bisa dilakukan tanpa login dengan tiket dan token.
Admin dapat mengubah status dan memberikan tanggapan.
Logging dan metadata tidak sembarangan menghubungkan identitas pegawai dengan laporan anonim.