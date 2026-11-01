# PROMPT AI AGENT — PENGEMBANGAN FITUR KELOLA PENGGUNA APLIKASI KOMPLAIN

Saya sedang mengembangkan aplikasi **KOMPLAIN**, yaitu aplikasi internal untuk pengajuan dan penanganan komplain pegawai di lingkungan instansi/dinas.

Tolong periksa terlebih dahulu struktur proyek, framework, database, sistem login, dan fitur yang sudah tersedia. Setelah itu, implementasikan pengembangan berikut dengan menyesuaikan kode yang sudah ada. **Jangan membuat ulang proyek dari awal dan jangan menghapus fitur yang sudah berjalan.**

## 1. Fitur Kelola Pengguna

Pertahankan fitur Kelola Pengguna yang sudah tersedia dan kembangkan agar Admin dapat:

- Melihat daftar pengguna beserta NIP, nama, hak akses, dan bidang tugas jika tersedia.
- Menambahkan pegawai secara manual melalui tombol **Tambah Pegawai**.
- Mengedit data pegawai.
- Mengaktifkan atau menonaktifkan akun.
- Mengatur hak akses pengguna.
- Mencari dan memfilter pengguna berdasarkan nama, NIP, peran, atau bidang tugas.
- Mencegah NIP duplikat.

Formulir Tambah Pegawai setidaknya memiliki:
- NIP.
- Nama lengkap.
- Password awal.
- Hak akses.
- Bidang tugas, apabila pengguna ditetapkan sebagai Petugas Bidang.

Sesuaikan field dengan struktur database yang sebenarnya. Jangan menambahkan kolom yang tidak diperlukan tanpa memeriksa kebutuhan dan skema database terlebih dahulu.

## 2. Hak Akses Pengguna

Aplikasi perlu mendukung tiga jenis fungsi pengguna:

**A. Admin**
- Mengelola akun pengguna.
- Mengatur hak akses.
- Melihat dan memverifikasi pengaduan.
- Mengklasifikasikan pengaduan berdasarkan bidang yang berwenang.
- Menugaskan pengaduan kepada Petugas Bidang.
- Memantau tindak lanjut dan status pengaduan.

**B. Pegawai**
- Login menggunakan NIP dan password.
- Mengajukan pengaduan.
- Melihat daftar dan status pengaduan miliknya sendiri.
- Melihat tanggapan atau perkembangan pengaduannya sesuai hak akses.

**C. Petugas Bidang**
- Melihat pengaduan yang ditugaskan kepada bidangnya.
- Melihat informasi yang diperlukan untuk menangani pengaduan.
- Memberikan tanggapan dan memperbarui status penanganan.
- Tidak boleh mengakses pengaduan yang tidak menjadi kewenangannya, kecuali diberikan izin secara eksplisit.

Jika seorang pegawai juga ditugaskan menjadi Petugas Bidang, usahakan agar ia tetap dapat mengajukan pengaduan sebagai pegawai. Gunakan mekanisme multi-role atau desain hak akses yang sesuai dengan struktur database yang sudah ada. Jangan langsung mengganti peran Pegawai menjadi Petugas Bidang apabila hal itu menyebabkan akses sebagai Pegawai hilang.

## 3. Penugasan Pengaduan ke Bidang Terkait

Buat atau kembangkan alur penugasan pengaduan:

1. Pegawai mengirimkan pengaduan.
2. Admin memverifikasi dan menentukan kategori atau bidang terkait.
3. Admin memilih Petugas Bidang yang berwenang.
4. Petugas menerima pengaduan pada dashboard tugasnya.
5. Petugas menindaklanjuti pengaduan dan memperbarui status.
6. Admin dapat memantau proses penanganan.
7. Pegawai yang mengajukan dapat melihat perkembangan pengaduannya.

Contoh bidang tugas dapat berupa Umum, Kepegawaian, Keuangan, Infrastruktur, atau bidang lainnya. Jangan menganggap daftar contoh tersebut sebagai daftar resmi; buat agar bidang dapat disesuaikan dengan kebutuhan instansi.

Sediakan status pengaduan yang konsisten dengan sistem yang sudah ada, misalnya Menunggu Verifikasi, Diteruskan, Sedang Ditangani, dan Selesai. Hindari perubahan status yang tidak sesuai kewenangan pengguna.

## 4. Kerahasiaan Identitas Pengadu

Kerahasiaan identitas pengadu merupakan bagian penting aplikasi KOMPLAIN.

- Identitas pengadu tidak boleh ditampilkan secara publik atau kepada petugas yang tidak berwenang.
- Admin hanya dapat mengakses identitas sesuai kewenangan yang ditetapkan.
- Petugas Bidang hanya melihat informasi identitas jika memang diperlukan dan diizinkan.
- Pengadu dapat melihat pengaduannya sendiri setelah login.
- Terapkan pemeriksaan hak akses di sisi backend, bukan hanya menyembunyikan tombol atau kolom pada tampilan.
- Hindari menampilkan NIP atau informasi sensitif dalam URL, log, atau pesan kesalahan.
- Jangan menganggap bahwa identitas sudah aman hanya karena tidak ditampilkan di halaman. Lindungi pula akses API dan database.

## 5. Persiapan Integrasi Database Pegawai Dinas

Untuk tahap pengembangan dan demo, Admin tetap harus dapat menambahkan data pegawai secara manual melalui fitur Tambah Pegawai.

Pada tahap berikutnya, aplikasi akan disiapkan agar dapat menggunakan data akun pegawai yang sudah tersedia di sistem dinas.

Ketentuan:
- Jangan membuat akun duplikat ketika integrasi dengan database dinas dilakukan.
- Periksa apakah sistem dinas menggunakan database yang sama, database terpisah, atau layanan autentikasi/API.
- Jangan mengasumsikan bahwa database dinas dapat diakses langsung.
- Pisahkan akun demo dari akun asli jika diperlukan.
- Rancang integrasi agar bisa dikembangkan kemudian tanpa harus membangun ulang fitur Kelola Pengguna.
- Jangan menyalin password dari database dinas atau menyimpan password dalam bentuk teks biasa.
- Jangan mengubah database atau sistem autentikasi eksternal tanpa izin dan konfigurasi yang jelas.

## 6. Keamanan dan Validasi

Pastikan implementasi mencakup:
- Validasi input di backend.
- Password disimpan menggunakan mekanisme hashing yang aman dan sesuai framework.
- Proteksi terhadap SQL injection dan akses tanpa otorisasi.
- Pembatasan akses berdasarkan peran dan kepemilikan data.
- Pencegahan perubahan hak akses oleh pengguna yang tidak berwenang.
- Penanganan kesalahan yang tidak membocorkan informasi sensitif.
- Konfirmasi sebelum menonaktifkan atau menghapus pengguna.
- Penggunaan transaksi database jika diperlukan untuk menjaga konsistensi data.

Jangan menghapus data pengaduan ketika akun pengguna dinonaktifkan. Pertahankan relasi dan riwayat pengaduan sesuai rancangan database.

## 7. Antarmuka

Pertahankan desain visual, navigasi, komponen, dan gaya antarmuka aplikasi KOMPLAIN yang sudah ada.

Buat tampilan yang:
- Konsisten dengan halaman lain.
- Responsif.
- Mudah digunakan oleh Admin.
- Memiliki notifikasi sukses dan gagal.
- Menampilkan status pengguna dan pengaduan secara jelas.
- Memiliki formulir dengan validasi yang mudah dipahami.

Jangan mengganti seluruh desain aplikasi jika tidak diperlukan.

## 8. Ketentuan Pengerjaan

Sebelum melakukan perubahan:

1. Periksa struktur folder dan file proyek.
2. Periksa skema tabel database, relasi, dan migration yang sudah ada.
3. Periksa middleware, autentikasi, routing, controller, model, serta tampilan yang berkaitan.
4. Identifikasi fitur yang sudah berfungsi agar tidak terjadi duplikasi.
5. Tentukan perubahan minimum yang diperlukan dan jelaskan rencananya.

Saat implementasi:

1. Gunakan teknologi dan konvensi yang sudah dipakai proyek.
2. Utamakan perubahan pada file yang relevan.
3. Buat migration hanya jika memang diperlukan.
4. Jangan menghapus atau mereset data yang sudah ada.
5. Jangan menimpa konfigurasi penting tanpa persetujuan.
6. Pastikan setiap tombol dan formulir benar-benar berfungsi, bukan sekadar tampilan.
7. Pastikan fitur lama tetap berjalan.

Setelah implementasi:

- Jelaskan file yang diubah dan fungsinya.
- Jelaskan perubahan database dan migration yang diperlukan.
- Berikan langkah pengujian untuk Admin, Pegawai, dan Petugas Bidang.
- Uji validasi NIP duplikat, pembatasan hak akses, kerahasiaan identitas, dan penugasan pengaduan.
- Laporkan bagian yang belum dapat diuji atau membutuhkan konfigurasi dari pihak dinas.
- Jangan mengklaim pengujian berhasil jika belum benar-benar dilakukan.

**Target akhir:** aplikasi KOMPLAIN memiliki fitur Kelola Pengguna dengan Tambah Pegawai manual untuk demo, pengaturan hak akses, persiapan integrasi akun pegawai dinas, serta alur penugasan pengaduan kepada Petugas Bidang, tanpa merusak fitur dan database yang sudah ada.

## 9. Konsistensi Desain Mobile-First Berdasarkan Role

Aplikasi KOMPLAIN sudah memiliki desain antarmuka dengan pendekatan **mobile-first untuk role Pegawai**. Pertahankan desain yang sudah ada dan terapkan pendekatan desain yang sama untuk role Petugas Bidang.

### A. Pegawai

- Pertahankan desain mobile-first yang sudah diterapkan.
- Jangan mengubah layout, navigasi, warna, komponen, atau pengalaman pengguna yang sudah berjalan, kecuali jika memang diperlukan untuk integrasi fitur baru.
- Pastikan seluruh fitur Pegawai tetap nyaman digunakan melalui smartphone.

### B. Petugas Bidang

- Gunakan pendekatan mobile-first yang konsisten dengan tampilan Pegawai.
- Gunakan kembali komponen UI, gaya visual, pola navigasi, spacing, tipografi, dan responsivitas yang sudah tersedia jika memungkinkan.
- Sesuaikan isi dashboard dan menu berdasarkan tugas Petugas Bidang, bukan dengan menyalin seluruh fitur Pegawai.
- Prioritaskan fitur yang diperlukan untuk melihat pengaduan yang ditugaskan, membaca detail pengaduan, memberikan tanggapan, dan memperbarui status penanganan.
- Pastikan tabel, kartu pengaduan, formulir, tombol tindakan, dan detail pengaduan nyaman digunakan pada layar smartphone.
- Pada layar yang lebih besar, layout boleh menyesuaikan secara responsif tanpa menghilangkan konsistensi desain.
- Jangan membuat desain Petugas Bidang yang berbeda sendiri dari desain Pegawai tanpa alasan yang jelas.

### C. Admin

- Pertahankan desain dashboard Admin yang sudah tersedia.
- Admin tidak wajib mengikuti layout mobile-first Pegawai dan Petugas Bidang.
- Prioritaskan kenyamanan pengelolaan data, tabel pengguna, pengaturan hak akses, verifikasi pengaduan, penugasan bidang, serta pemantauan tindak lanjut pada layar desktop.
- Tetap pertahankan fungsi yang sudah ada dan jangan merombak dashboard Admin secara keseluruhan.
- Pastikan tampilan Admin tetap dapat digunakan pada layar yang lebih kecil jika desain yang ada mendukungnya.

### D. Ketentuan Implementasi

Sebelum membuat tampilan Petugas Bidang, periksa terlebih dahulu implementasi UI Pegawai yang sudah ada. Gunakan kembali komponen dan layout yang relevan agar desain tetap konsisten dan menghindari duplikasi kode.

Jangan mengubah seluruh desain aplikasi. Fokus pada penambahan antarmuka Petugas Bidang dan penyesuaian minimum yang diperlukan untuk mendukung role baru.

**Tujuan:** Pegawai dan Petugas Bidang mendapatkan pengalaman penggunaan mobile-first yang konsisten, sedangkan Admin tetap menggunakan dashboard pengelolaan yang sesuai dengan kebutuhan administrasi.

### Ketentuan Penting: Pertahankan Desain dan Tampilan yang Sudah Ada

**Jangan mengubah desain, tampilan, maupun konten yang sudah ada pada aplikasi KOMPLAIN.** Jadikan implementasi saat ini sebagai acuan utama.

- Jangan mengubah tulisan, judul halaman, label, nama menu, teks tombol, istilah, warna, font, ikon, ukuran, spacing, layout, navigasi, maupun komponen yang sudah ada.
- Jangan mengganti atau menulis ulang konten yang sudah tersedia, termasuk teks pada dashboard, halaman Pegawai, dan halaman Admin.
- Jangan merombak struktur halaman, mengubah alur penggunaan, atau menghapus fitur yang sudah berjalan.
- Jangan membuat desain baru jika komponen atau pola desain yang sesuai sudah tersedia.
- Untuk role Petugas Bidang, **ikuti dan sesuaikan dengan desain mobile-first Pegawai yang sudah ada**, menggunakan gaya visual dan komponen yang sama. Tambahkan hanya menu, informasi, dan tindakan yang memang diperlukan untuk tugas Petugas Bidang.
- Untuk role Admin, pertahankan tampilan dan desain Admin yang sudah ada. Jangan memaksakan desain mobile-first Pegawai ke dashboard Admin.
- Jika memungkinkan, gunakan kembali komponen, layout, dan stylesheet yang sudah tersedia daripada membuat versi baru.
- Lakukan perubahan seminimal mungkin dan hanya pada bagian yang diperlukan untuk menambahkan fitur baru serta mendukung role Petugas Bidang.

**Sebelum melakukan perubahan, periksa kode dan tampilan yang sudah ada. Jangan menebak atau mengganti elemen yang sudah berjalan. Jika ada kebutuhan yang mengharuskan perubahan pada desain atau konten lama, pertahankan kondisi awal dan minta persetujuan terlebih dahulu.**

**Tujuan utama:** menambahkan fungsionalitas baru tanpa mengubah identitas visual, tulisan, konten, dan pengalaman penggunaan aplikasi KOMPLAIN yang sudah ada.