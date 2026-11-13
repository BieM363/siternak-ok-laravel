# 🐄 Buku Saku Presentasi: SiTernak-OK
### **Dinas Peternakan dan Perkebunan Provinsi Gorontalo**
*(Disusun dengan Bahasa Sederhana & Mudah Dipahami untuk Persiapan Presentasi)*

---

## 🎯 1. Apa Itu Web SiTernak-OK? (Penjelasan Bahasa Kelas 6 SD)

Bayangkan **SiTernak-OK** ini seperti **"Buku Kesehatan Ibu dan Anak (Buku Pink Posyandu)"**, tapi khusus untuk **Sapi Betina** milik peternak di seluruh Provinsi Gorontalo!

### Masalah Sebelumnya:
Dulu, ketika petugas lapangan (**Inseminator**) menyuntikkan bibit kehamilan (**Inseminasi Buatan / IB**) ke sapi milik warga di desa-desa:
- Catatannya sering masih di kertas atau buku tulis, sehingga gampang tercecer.
- Petugas dan peternak sering **lupa kapan harus mengecek** apakah sapinya benar-benar hamil atau gagal.
- Dinas sulit mengetahui **kapan sapi akan melahirkan (HPL)** dan berapa banyak **anak sapi (pedet)** yang sudah lahir di tiap kabupaten/kota.

### Solusi SiTernak-OK:
Dengan web **SiTernak-OK**:
1. Petugas cukup mencatat **1 kali tanggal suntik IB** lewat HP mereka saat di kandang.
2. Web akan **menghitung otomatis** kapan sapi harus diperiksa (umur 30 hari, 90 hari, 245 hari) sampai kapan sapi itu akan melahirkan (**283 hari / sekitar 9 bulan 10 hari**).
3. Ketika waktunya tiba, web akan **mengirimkan bunyi pengingat (notifikasi) langsung ke browser HP petugas**, jadi petugas di lapangan tidak perlu repot membawa laptop ke kandang!

> **Kalimat Kunci Saat Pembukaan Presentasi:**
> *"SiTernak-OK adalah asisten digital bagi Dinas Peternakan dan Perkebunan Provinsi Gorontalo untuk memantau kehamilan sapi sejak hari pertama disuntik IB, mengingatkan petugas lapangan lewat browser HP mereka, hingga mencatat kelahiran anak sapi secara akurat di seluruh 6 Kabupaten/Kota."*

---

## 👥 2. Ada Berapa Role (Peran Pengguna)?

Secara teknis di dalam database (`users.role`), ada **3 kategori dasar** (`admin`, `operator`, dan `guest`), namun di dalam aplikasi dibagi menjadi **5 Peran Kerja Nyata** sesuai struktur Dinas di Provinsi Gorontalo:

| No | Nama Role | Ibarat di Sekolah / Kehidupan | Apa Saja Tugas & Hak Aksesnya? |
| :-: | :--- | :--- | :--- |
| **1** | **Admin Provinsi** *(Super Admin)* | **Kepala Sekolah** | Melihat dan mengatur **seluruh data di 6 Kabupaten/Kota se-Gorontalo**. Bisa menambah/mengedit data wilayah, akun petugas, peternak, bibit straw, hingga mencetak laporan resmi Provinsi. |
| **2** | **Admin Kota Gorontalo** | **Wali Kelas Kota** | Mengurus data khusus wilayah **Kota Gorontalo (9 Kecamatan & 50 Kelurahan)**. Istilah wilayahnya otomatis memakai kata **"Kelurahan"** (bukan Desa). |
| **3** | **Admin Kabupaten** | **Wali Kelas Kabupaten** | Mengurus data khusus di **1 Kabupaten saja** (Kab. Gorontalo, Boalemo, Bone Bolango, Pohuwato, atau Gorontalo Utara). Tidak bisa mengutak-atik data kabupaten tetangga. |
| **4** | **Operator Lapangan** *(Petugas Inseminator)* | **Dokter / Petugas Posyandu Keliling** | Petugas yang turun langsung ke kandang warga. **Hanya Operator yang boleh mendaftarkan suntik IB baru**, menekan tombol konfirmasi **YA / TIDAK** saat mengecek kehamilan sapi, dan mencatat kelahiran anak sapi. Waktu loginnya dibatasi **8 jam kerja** dan mendapat notifikasi langsung di **browser HP**. |
| **5** | **Tamu / Publik** *(Mode Lihat Saja)* | **Tamu Pameran** | Masyarakat umum yang membuka web tanpa login. Hanya bisa melihat grafik kemajuan, jumlah sapi, dan peringkat petugas. **NIK KTP peternak otomatis disensor (`7502************`)** agar data pribadi warga tetap aman. |

---

## 🌟 3. Fitur-Fitur Utama Aplikasi (Apa Saja Kehebatannya?)

Berikut adalah **9 fitur andalan** yang wajib disebutkan saat presentasi:

1. **Pembuat Nomor Induk Sapi (NIK Sapi) Otomatis**:
   Begitu petugas memilih Desa/Kelurahan tempat sapi berada, web langsung membuatkan nomor identitas unik untuk sapi tersebut berdasarkan kode resmi wilayah Gorontalo (Contoh: `75.02.01.2001.26.0001`).

2. **Hitung Otomatis Jadwal Kehamilan Sapi (30 – 90 – 245 – 283 Hari)**:
   Petugas hanya perlu memasukkan **Tanggal Suntik IB**. Sistem langsung menghitung 4 tanggal penting:
   - **Hari ke-30**: Cek Awal (apakah birahi lagi atau sudah mulai bunting).
   - **Hari ke-90**: Pemeriksaan Kebuntingan (PKB) oleh petugas.
   - **Hari ke-245**: Persiapan kandang melahirkan.
   - **Hari ke-283**: Hari Perkiraan Lahir (HPL).

3. **Otomatis Lanjut Jika Lupa Dikonfirmasi 3 Hari (*Auto-Advance 3 Hari*)**:
   Kalau dalam **3 hari** sejak jadwal cek petugas belum sempat menekan tombol YA/TIDAK, sistem **otomatis memajukan status sapi ke tahap berikutnya** supaya pemantauan tidak macet.

4. **Notifikasi Lewat Peramban Ponsel (HP) & Batas Login 8 Jam Operator**:
   - **Notifikasi Semua Browser HP**: Pengingat sapi yang harus dicek muncul langsung di layar/notifikasi peramban ponsel (Chrome HP, Safari, Samsung Internet, dll.) lengkap dengan getar dan nada dering karena petugas di lapangan membawa HP, bukan laptop.
   - **Cronjob Sesi Kerja 8 Jam**: Waktu login operator dibatasi **maksimal 8 jam**. Ada hitung mundur waktu di layar HP (`08:00:00`), peringatan saat sisa 15 menit, dan otomatis *logout* lewat *cronjob* setelah 8 jam.

5. **Database Bibit Sperma Beku / Straw Pejantan (`BullStraw`)**:
   Menyimpan daftar kode bibit sapi pejantan unggul (seperti Limosin `LIM-8890`, Simental `SIM-5520`, Brahman, PO) dari Balai Inseminasi.

6. **Pencatatan Kelahiran Anak Sapi (Pedet) & Fitur "Tarik ke Sapi Siap IB"**:
   Saat sapi melahirkan, petugas mengisi data anak sapi. Jika anak sapi tersebut **Betina** dan sudah besar, tombol **"Tarik ke Sapi Siap IB"** langsung mengubahnya menjadi **Sapi Indukan Dewasa** baru dengan 1 klik.

7. **Papan Peringkat (*Leaderboard*) Petugas Inseminator**:
   Menampilkan ranking petugas lapangan berdasarkan jumlah IB, tingkat keberhasilan hamil (*Conception Rate*), dan kelahiran (*Calving Rate*).

8. **Cetak Laporan Resmi PDF (Tanda Tangan Kepala Dinas) & Excel**:
   Rekapan kelahiran anak sapi bisa diunduh ke PDF resmi ber-Kop Dinas lengkap dengan tanda tangan Kepala Dinas (**Ir. Ramdhan Pade, M.Si**) maupun file Excel.

9. **Sensor Otomatis NIK KTP Peternak (Keamanan Privasi Warga)**:
   Jika web dibuka oleh publik tanpa login, NIK KTP peternak otomatis disensor demi keamanan data pribadi warga.

---

## 🎮 4. Daftar Controller & Kegunaannya
*(Ibarat "Manajer/Petugas Bagian" di dalam kode program yang mengatur tugas masing-masing halaman)*

Semua file Controller berada di folder `app/Http/Controllers/`:

| No | Nama Controller | Ibarat Petugas Apa? | Digunakan Untuk Apa Saja? |
| :-: | :--- | :--- | :--- |
| **1** | `AuthController.php` | **Petugas Pintu Masuk (Satpam)** | Mengatur halaman depan (*Home*), proses **Login** (termasuk mengaktifkan batas waktu 8 jam untuk Operator), **Logout**, dan akses mode Tamu. |
| **2** | `DashboardController.php` | **Petugas Papan Informasi Utama** | Menghitung total sapi, sapi bunting, sapi siap melahirkan, daftar pengingat 3 hari, serta memfilter tabel berdasarkan kabupaten, kecamatan, desa, dan petugas. |
| **3** | `CowController.php` | **Petugas Pendaftaran Sapi Induk** | Mengurus daftar sapi betina, menambah sapi baru sekaligus membuatkan **NIK Sapi Otomatis**, melihat kartu detail sapi, mengedit, dan menghapus data sapi. |
| **4** | `PregnancyController.php` | **Dokter Kandungan Sapi** | Mencatat suntikan IB baru, menghitung tanggal 30/90/245/283 hari, memproses tombol konfirmasi **YA / TIDAK** dari operator, serta mencatat kelahiran anak sapi baru. |
| **5** | `CalfController.php` | **Petugas Kamar Bayi Sapi (Pedet)** | Menampilkan daftar anak sapi yang sudah lahir, mengedit datanya, dan menjalankan fitur **"Promosikan / Tarik Anak Sapi Betina menjadi Sapi Indukan"**. |
| **6** | `BullStrawController.php` | **Petugas Gudang Bibit (Straw)** | Mengelola master data kode bibit sperma beku pejantan (tambah kode straw baru, edit nama pejantan/balai, aktifkan/nonaktifkan, atau hapus). |
| **7** | `FarmerController.php` | **Petugas Data Warga Peternak** | Mengelola data bapak/ibu pemilik sapi (tambah peternak, edit alamat desa & nomor HP, hapus, serta pencarian nama peternak otomatis). |
| **8** | `RegionController.php` | **Petugas Peta Wilayah** | Mengelola data wilayah Provinsi Gorontalo mulai dari tingkat **Kabupaten/Kota (6)**, **Kecamatan (77)**, hingga **Desa/Kelurahan (722)**. |
| **9** | `ReportController.php` | **Petugas Bagian Laporan & Juara** | Menyusun halaman **Rekapan Kelahiran Pedet**, membuat cetakan **PDF Resmi & Excel**, serta menghitung skor **Leaderboard Inseminator**. |
| **10** | `UserController.php` | **Petugas Bagian SDM / Akun** | Digunakan oleh Admin untuk menambah, mengedit, atau menghapus akun pengguna (Admin Provinsi, Admin Kab/Kota, dan Operator Lapangan). |
| **11** | `NotificationController.php` | **Petugas Pengirim Pengingat HP** | Mengirimkan data pengingat sapi yang harus dikonfirmasi dan sisa waktu login 8 jam secara *real-time* ke peramban ponsel (HP) petugas. |

---

## 🗄️ 5. Isi Database (Tabel Apa Saja & Menyimpan Apa?)
*(Ibarat "Lemari Arsip Digital" yang punya beberapa laci/tabel yang saling terhubung)*

| No | Nama Tabel | Bahasa Sederhananya | Apa Saja Isi di Dalamnya? |
| :-: | :--- | :--- | :--- |
| **1** | `users` | **Laci Akun Pengguna & Petugas** | Nama petugas/admin, email, password, jabatan (`role`: admin/operator), wilayah tugas (`regency_id`), nomor HP, waktu login terakhir (`last_login_at`), dan **batas waktu berakhirnya login 8 jam** (`login_expires_at`). |
| **2** | `regencies` | **Laci Kabupaten & Kota** | Kode wilayah dan nama **6 Kabupaten/Kota** di Provinsi Gorontalo (Kab. Gorontalo, Boalemo, Bone Bolango, Pohuwato, Gorontalo Utara, dan Kota Gorontalo). |
| **3** | `districts` | **Laci Kecamatan** | Kode dan nama **77 Kecamatan** beserta sambungan ke Kabupaten/Kota induknya. |
| **4** | `villages` | **Laci Desa & Kelurahan** | Kode dan nama **722 Desa/Kelurahan** beserta sambungan ke Kecamatan induknya (menjadi dasar pembuatan NIK Sapi otomatis). |
| **5** | `farmers` | **Laci Data Peternak (Pemilik Sapi)** | NIK KTP peternak, nama lengkap peternak, nomor telepon/WA, serta lokasi kabupaten, kecamatan, dan desa tempat tinggalnya. |
| **6** | `bull_straws` | **Laci Katalog Bibit Pejantan (Straw)** | Kode straw unik (misal `LIM-8890`), nama sapi pejantan, jenis ras (Limosin/Simental/Brahman/PO), nama balai produsen, dan status aktif/tidaknya bibit. |
| **7** | `cows` | **Laci Data Sapi Betina Indukan** | **NIK Sapi otomatis** (`cow_nik`), nama panggilan sapi, ras sapi, tanggal lahir, siapa peternak pemiliknya (`farmer_id`), lokasi desanya, dan status sapi sekarang (*Pasca IB, Bunting, Siap Melahirkan, Pasca Melahirkan, atau Tidak Bunting*). |
| **8** | `pregnancy_records` | **Laci Rekam Medis Kehamilan Sapi (Inti Sistem!)** | ID sapi yang disuntik, ID operator yang menyuntik (`operator_id`), kode straw, **Tanggal Suntik IB**, **Tanggal Cek 30 Hari**, **Tanggal PKB 90 Hari**, **Tanggal Siap Lahir 245 Hari**, **Tanggal HPL (283 Hari)**, status tahapan saat ini, dan catatan pemeriksaan. |
| **9** | `calves` | **Laci Data Anak Sapi (Pedet)** | NIK anak sapi (`PEDET-...`), nama anak sapi, ras, jenis kelamin (*Jantan/Betina*), tanggal lahir, berat badan saat lahir (kg), kondisi (*Sehat/Cacat/Mati*), siapa induknya (`cow_id`), dan penanda apakah sudah dipromosikan jadi sapi indukan dewasa. |
| **10** | `sessions` | **Laci Sesi Login Aktif** | Mencatat siapa saja yang sedang login di browser HP/laptop saat ini (dibersihkan otomatis oleh *cronjob* jika waktu login operator sudah lewat **8 jam**). |

---

## 💡 6. Tips Singkat Saat Demo di Depan Dinas Besok

1. **Mulai dari Halaman Utama (`/`) tanpa login**: Tunjukkan bahwa masyarakat umum bisa melihat statistik Provinsi Gorontalo secara transparan, tapi NIK KTP peternak aman karena disensor otomatis.
2. **Login sebagai Operator Lapangan (`operator@siternak.go.id` / password: `password`)**:
   - Tunjukkan **hitung mundur waktu kerja 8 jam** (`08:00:00`) di bagian atas layar.
   - Klik tombol **"Tes Notifikasi HP"** atau ikon **Lonceng** di pojok kanan atas untuk mendemokan bagaimana petugas di lapangan menerima pengingat langsung di browser ponsel mereka tanpa perlu membawa laptop.
   - Tunjukkan cara **Pendaftaran IB Baru** (NIK sapi keluar otomatis saat pilih desa) dan cara menekan tombol **YA / TIDAK** pada kartu pengingat 3 hari.
3. **Login sebagai Admin Provinsi (`admin@siternak.go.id` / password: `password`)**:
   - Buka menu **Kelahiran (`/reports`)** lalu klik **Export PDF** untuk memperlihatkan laporan resmi siap cetak dengan Kop Dinas Peternakan & Perkebunan Provinsi Gorontalo beserta tanda tangan Kepala Dinas.
   - Buka menu **Inseminator (`/inseminators`)** untuk memperlihatkan papan peringkat kinerja petugas lapangan.

