# 🐄 SiTernak-OK — Sistem Pemantauan Kehamilan Sapi
### **Dinas Peternakan dan Perkebunan Provinsi Gorontalo**

Aplikasi sistem informasi berbasis web (**Laravel**) yang dirancang khusus untuk **Dinas Peternakan dan Perkebunan Provinsi Gorontalo** guna memantau siklus kebuntingan sapi betina hasil Inseminasi Buatan (IB), memperhitungkan Hari Perkiraan Lahir (HPL), mengotomatisasi pengingat (*milestone reminder*), mencatat kelahiran anak sapi (*pedet*), mengelola master Kode Straw Pejantan IB, mengelola data administratif wilayah se-Provinsi Gorontalo (Kabupaten/Kota, Kecamatan, Desa/Kelurahan), serta menyajikan papan peringkat (*Leaderboard*) performa inseminator.

---

## 📌 Daftar Isi
1. [Fitur Utama & Alur Bisnis](#-fitur-utama--alur-bisnis)
2. [Hierarki Hak Akses & Tampilan Home Berbasis Role](#-hierarki-hak-akses--tampilan-home-berbasis-role)
3. [Siklus Kebuntingan & Otomatisasi Reminder 3 Hari](#-siklus-kebuntingan--otomatisasi-reminder-3-hari)
4. [Master Kode Straw Pejantan IB & Promosi Pedet Betina](#-master-kode-straw-pejantan-ib--promosi-pedet-betina)
5. [Pengelolaan Wilayah Administratif Gorontalo](#-pengelolaan-wilayah-administratif-gorontalo)
6. [Privasi & Keamanan Data (Sensor NIK Publik)](#-privasi--keamanan-data-sensor-nik-publik)
7. [Persyaratan Sistem](#-persyaratan-sistem)
8. [Panduan Instalasi & Setup Database](#-panduan-instalasi--setup-database)
9. [Kredensial Akun Login Demo](#-kredensial-akun-login-demo)
10. [Struktur Folder & Arsitektur Kode](#-struktur-folder--arsitektur-kode)

---

## 🌟 Fitur Utama & Alur Bisnis

1. **Formulasi NIK Sapi Otomatis Berbasis Wilayah Gorontalo**:
   - Format NIK Sapi: `[Kode_Kecamatan].[Kode_Desa].[Tahun].[Nomor_Urut_4_Digit]` (Contoh: `75.02.01.2001.26.0001`).
   - Struktur hirarki wilayah mencakup **6 Kabupaten/Kota, 77 Kecamatan, dan 722 Desa/Kelurahan** di seluruh Provinsi Gorontalo.

2. **Khusus Ternak Sapi Betina**:
   - Seluruh populasi ternak utama dalam sistem ini adalah sapi betina produktif yang dipantau siklus kehamilannya pasca-IB.

3. **Master Database Kode Straw Pejantan IB (Bisa Tambah, Edit, Hapus)**:
   - Manajemen kode semen beku pejantan (misal: `LIM-8890`, `SIM-5520`, `PO-1102`, `BRM-9912`) dari BBIB Singosari/BIB Lembang dengan fitur status aktif/nonaktif dan pemilihan otomatis saat pendaftaran IB baru.

4. **Pendaftaran IB Baru Khusus Operator Inseminator**:
   - Petugas inseminator lapangan memiliki kewenangan khusus mendaftarkan rekam IB baru dan sapi betina baru, sementara admin wilayah fokus pada supervisi dan administrasi data.

5. **Peringkat Kinerja & Rekapitulasi Kelahiran**:
   - **Leaderboard Inseminator**: Memperingkatkan inseminator berdasarkan total tindakan IB, kebuntingan positif, pedet lahir, serta nilai *Conception Rate* (CR %).
   - **Rekapan Kelahiran Pedet (Filter Otomatis 1 Jan 2025 - Sekarang)**: Menampilkan statistik dan rincian kelahiran pedet yang secara otomatis mencakup data dari tanggal **1 Januari 2025 hingga hari ini**, rasio kelamin pedet (Jantan vs Betina), metrik *Calving Rate* (CvR %), silsilah induk, serta filter kustom (ras, inseminator, rentang tanggal manual).

6. **Promosi Pedet Betina ("Tarik ke Sapi Siap IB")**:
   - Anak sapi betina yang lahir dapat langsung dipromosikan menjadi sapi indukan baru dengan 1-klik, otomatis mengalokasikan NIK ternak induk baru.

7. **Ekspor Laporan Resmi (PDF & Excel)**:
   - Cetak laporan rekapitulasi kelahiran berformat **PDF resmi dengan Kop Dinas Peternakan dan Perkebunan Provinsi Gorontalo**, filter periode tanggal aktif, serta tanda tangan resmi Kepala Dinas (**Ir. Ramdhan Pade, M.Si** - NIP. 19691115 200212 1 008).
   - Ekspor data lengkap ke format **Excel / CSV** dengan penamaan file otomatis berdasarkan rentang tanggal.

8. **Antarmuka Modern & Optimalisasi Layar Ponsel (*Mobile UI/UX*)**:
   - Tampilan responsif dengan Tailwind CSS & Alpine.js, mode Gelap/Terang, tombol aksi adaptif, serta menu drawer terisolasi di root overlay agar tidak menimpa konten ponsel.

9. **Cronjob Sesi Login 8 Jam Operator & Notifikasi Peramban Ponsel (HP) Saat Turun Lapangan**:
   - Waktu login **Operator Inseminator** dibatasi maksimal **8 jam kerja** (`480 menit`) melalui *middleware* dan *cronjob* otomatis (`ternak:operator-session-cron`), dilengkapi penghitung waktu mundur (*live countdown* `08:00:00`) di layar HP.
   - Pengingat konfirmasi sapi dikirimkan melalui **Service Worker (`/sw.js`) & Banner Push Melayang** yang kompatibel di semua peramban ponsel (**Chrome Android, Safari iOS, Samsung Internet, Firefox Mobile, Opera/Edge**) lengkap dengan getar (`vibrate`) dan nada dering, sehingga petugas di lapangan cukup menggunakan ponsel tanpa perlu membawa laptop.

---

## 👥 Hierarki Hak Akses & Tampilan Home Berbasis Role

Halaman depan (**Home**) dan dashboard secara dinamis menyesuaikan banner, statistik ringkas, dan 6 kartu aksi cepat sesuai peran pengguna:

| Role Akun | Scope Wilayah | Tema Visual | Hak Akses & Fitur Utama |
| :--- | :--- | :--- | :--- |
| 👑 **Admin Provinsi** | Seluruh Prov. Gorontalo (6 Kab/Kota) | *Royal Purple & Emerald* | **Super Admin**: Kelola seluruh sapi se-provinsi, peternak, data wilayah (Kab/Kota, Kec, Desa), kelola user/role, master straw, dan rekapitulasi laporan provinsi. |
| 🏛️ **Admin Kota Gorontalo** | Kota Gorontalo (9 Kec & 50 Kelurahan) | *Modern Teal & Cyan* | **Admin Kota**: Menggunakan terminologi **Kelurahan**, mengedit data 50 kelurahan & kecamatan kota, data peternak kota, user/operator kota, dan laporan kelahiran kota. |
| 🛡️ **Admin Kabupaten** | Kabupaten Lokal (Kab. Gorontalo, Boalemo, Bone Bolango, Pohuwato, Gorut) | *Ocean Sapphire Blue* | **Admin Kab**: Menggunakan terminologi **Desa**, mengedit data desa & kecamatan lokal, peternak lokal, operator lokal, dan laporan lokal. |
| 💉 **Operator Lapangan** | Wilayah Pelayanan IB Sendiri | *High-Energy Emerald & Amber* | **Petugas Inseminator**: Pendaftaran IB & Sapi Baru, konfirmasi milestone kebuntingan (Batas 3 Hari), tandai gagal/gugur, database straw, dan leaderboard skor IB. |
| 👁️ **Pengunjung / Tamu** | Seluruh Wilayah (Read-Only) | *Clean Slate Blue* | **Publik**: Mode pemantauan umum, melihat statistik agregat dan leaderboard. NIK Peternak disensor otomatis demi privasi. |

---

## ⏰ Siklus Kebuntingan & Otomatisasi Reminder 3 Hari

Setelah sapi betina diinseminasi, sistem otomatis membentuk 4 milestone utama:

```
[Hari 0] Suntik Inseminasi Buatan (IB)
   │
   ├──► [Hari 30 Pasca IB]   : Estimasi 30 Hari (Cek Birahi Kembali / Bunting Awal)
   ├──► [Hari 90 Pasca IB]   : Pemeriksaan Kebuntingan (PKB 90 Hari oleh Petugas)
   ├──► [Hari 245 Pasca IB]  : Persiapan Kandang Melahirkan (Siap Melahirkan)
   └──► [Hari 283 Pasca IB]  : HPL (Hari Perkiraan Lahir Normal, Jendela s/d +50 Hari)
```

### ⚡ Aturan Auto-Advance 3 Hari & Cronjob Sesi 8 Jam Operator
* Jika jadwal milestone (Hari 30, 90, atau 245) melewati **3 hari kalender tanpa konfirmasi manual**, sistem otomatis memajukan fase sapi ke tahap berikutnya agar rantai pemantauan tidak berhenti.
* Tombol **Check Auto-Advance (3 Hari)** dan **Tes Notifikasi HP** tersedia pada header/dashboard untuk simulasi atau eksekusi instan.
* **Cronjob Sesi 8 Jam & Notifikasi Peramban Ponsel**:
  * Memastikan sesi login operator lapangan berakhir otomatis setelah **8 jam** sejak login, memberikan peringatan saat tersisa **15 menit**, serta menyinkronkan pengingat konfirmasi sapi ke peramban ponsel.
* Perintah Artisan Scheduler:
  ```bash
  # Menjalankan cronjob pembatasan sesi 8 jam operator & sinkronisasi pengingat HP
  php artisan ternak:operator-session-cron

  # Menjalankan pengecekan auto-advance status kebuntingan 3 hari
  php artisan ternak:update-pregnancy-status

  # Menjalankan worker penjadwal otomatis (berjalan terus di latar belakang)
  php artisan schedule:work
  ```

---

## 🧪 Master Kode Straw Pejantan IB & Promosi Pedet Betina

### 🧬 Master Kode Straw Pejantan (`BullStraw`)
* Dikelola melalui menu khusus `/straws`.
* Fitur mencakup: **Tambah Straw Baru**, **Edit Data Straw** (Kode unik, Nama Bull, Ras, Balai Produsen, Status Aktif/Nonaktif, Catatan Genetik), dan **Hapus Straw**.
* Pilihan kode straw otomatis muncul di form pendaftaran IB sapi baru.

### 🐄 Promosi Pedet Betina
* Pada menu Data Anak Sapi (`/calves`), setiap pedet betina memiliki tombol **"Tarik ke Sapi Siap IB"**.
* Aksi ini otomatis mempromosikan pedet menjadi sapi betina indukan di tabel `cows` dengan NIK induk baru.

---

## 🗺️ Pengelolaan Wilayah Administratif Gorontalo

Data seluruh wilayah Provinsi Gorontalo tersimpan lengkap dan dapat dikelola pada menu `/regions`:

* **Admin Provinsi**: Dapat menambah dan mengedit nama/kode **Kabupaten/Kota**, **Kecamatan**, dan **Desa/Kelurahan**.
* **Admin Kota**: Dapat mengedit nama/kode **Kecamatan** dan **Kelurahan** di Kota Gorontalo.
* **Admin Kabupaten**: Dapat mengedit nama/kode **Kecamatan** dan **Desa** di Kabupaten terkait.

---

## 🔒 Privasi & Keamanan Data (Sensor NIK Publik)

* **Pengunjung Publik / Tamu**: NIK KTP Peternak disensor otomatis menjadi `7502************` atau disembunyikan pada seluruh kartu detail sapi dan direktori guna mematuhi prinsip perlindungan data pribadi (UU PDP).
* **Pengguna Login (Admin & Operator)**: NIK KTP Peternak tampil lengkap untuk keperluan verifikasi lapangan.

---

## 🛠️ Persyaratan Sistem

- **PHP**: Versi 8.2 atau lebih baru
- **Composer**: Package Manager PHP
- **Node.js & NPM**: Untuk build asset front-end
- **Database**: SQLite (Default bawaan langsung siap pakai) atau MySQL / MariaDB (XAMPP)

---

## 🚀 Panduan Instalasi & Setup Database

### Opsi A: Menggunakan Database SQLite (Paling Cepat & Langsung Jalan)

1. **Clone Repositori**:
   ```bash
   git clone https://github.com/BieM363/siternak-ok-laravel.git
   cd siternak-ok-laravel
   ```

2. **Install Dependensi**:
   ```bash
   composer install
   npm install
   ```

3. **Setup Environment**:
   Salin `.env.example` ke `.env`:
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```
   Pastikan pengaturan di `.env`:
   ```env
   DB_CONNECTION=sqlite
   ```

4. **Migrasi & Seeding Database Lengkap**:
   ```bash
   php artisan migrate:fresh --seed
   npm run build
   ```

5. **Jalankan Server Lokal**:
   ```bash
   php artisan serve
   ```
   Buka di browser: `http://127.0.0.1:8000`.

---

### Opsi B: Menggunakan Database MySQL (XAMPP)

1. Buat database baru bernama `siternak_ok` di **phpMyAdmin** (`http://localhost/phpmyadmin`).
2. Ubah konfigurasi `.env`:
   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=siternak_ok
   DB_USERNAME=root
   DB_PASSWORD=
   ```
3. Jalankan migrasi dan seed data Gorontalo:
   ```bash
   php artisan migrate:fresh --seed
   npm run build
   php artisan serve
   ```

---

## 🔑 Kredensial Akun Login Demo

Semua password akun demo di bawah adalah: `password`

| Peran / Jabatan | Email Login | Password | Cakupan Wilayah |
| :--- | :--- | :--- | :--- |
| **Admin Provinsi Gorontalo** | `admin@siternak.go.id` | `password` | Seluruh 6 Kab/Kota se-Gorontalo |
| **Admin Kota Gorontalo** | `admin.kotagorontalo@siternak.go.id` | `password` | Khusus 9 Kec & 50 Kelurahan Kota |
| **Admin Kab. Gorontalo** | `admin.gorontalo@siternak.go.id` | `password` | Khusus Kabupaten Gorontalo |
| **Admin Kab. Boalemo** | `admin.boalemo@siternak.go.id` | `password` | Khusus Kabupaten Boalemo |
| **Admin Kab. Bone Bolango** | `admin.bonebolango@siternak.go.id` | `password` | Khusus Kabupaten Bone Bolango |
| **Admin Kab. Pohuwato** | `admin.pohuwato@siternak.go.id` | `password` | Khusus Kabupaten Pohuwato |
| **Admin Kab. Gorontalo Utara**| `admin.gorut@siternak.go.id` | `password` | Khusus Kabupaten Gorontalo Utara |
| **Operator Inseminator (Budi)**| `operator@siternak.go.id` | `password` | Lapangan Inseminasi Kab. Gorontalo |
| **Operator Inseminator (Ahmad)**| `operator.kota@siternak.go.id` | `password` | Lapangan Inseminasi Kota Gorontalo |
| **Tamu / Pengunjung Publik** | *(Tanpa Login)* | - | Akses Publik / Read-Only |

---

## 📂 Struktur Folder & Arsitektur Kode

```
siternak-ok-laravel/
├── app/
│   ├── Console/Commands/
│   │   ├── AutoAdvancePregnancyPhases.php  # Otomatisasi milestone 3 hari tanpa konfirmasi
│   │   ├── OperatorSessionCron.php         # Cronjob batas login 8 jam operator & pengingat HP
│   │   ├── ScrapeGorontaloRegions.php      # Command scraping data 6 Kab/Kota se-Gorontalo
│   │   └── UpdatePregnancyStatus.php       # Command update otomatis status kebuntingan sapi
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── AuthController.php          # Login (sesi 8 jam operator), Logout & Home per-Role
│   │   │   ├── BullStrawController.php     # Manajemen Database Straw Pejantan (CRUD)
│   │   │   ├── CalfController.php          # Data Anak Sapi & Promosi Pedet Betina
│   │   │   ├── CowController.php           # Direktori Sapi Betina & Scoping Regional
│   │   │   ├── DashboardController.php     # Dashboard utama, status kebuntingan, filter
│   │   │   ├── FarmerController.php        # Data Peternak & Proteksi NIK
│   │   │   ├── NotificationController.php  # API polling notifikasi peramban ponsel & sesi 8 jam
│   │   │   ├── PregnancyController.php     # Pendaftaran IB, Konfirmasi & Tandai Gagal
│   │   │   ├── RegionController.php        # Manajemen Hirarki Wilayah (CRUD Kab, Kec, Desa/Kel)
│   │   │   ├── ReportController.php        # Leaderboard Inseminator & Rekapitulasi Kelahiran
│   │   │   └── UserController.php          # Manajemen Akun & Role Pengguna
│   │   └── Middleware/
│   │       ├── OperatorSessionTimeout.php  # Proteksi batas waktu login maksimal 8 jam operator
│   │       └── RoleMiddleware.php          # Proteksi hak akses berbasis role
│   ├── Models/
│   │   ├── BullStraw.php                   # Model Kode Straw Pejantan
│   │   ├── Calf.php                        # Model Anak Sapi (Pedet)
│   │   ├── Cow.php                         # Model Sapi Indukan Betina
│   │   ├── District.php                    # Model Kecamatan
│   │   ├── Farmer.php                      # Model Peternak
│   │   ├── PregnancyRecord.php             # Model Rekam Kebuntingan & HPL
│   │   ├── Regency.php                     # Model Kabupaten / Kota
│   │   ├── User.php                        # Model Pengguna, Role & Helper Sesi 8 Jam
│   │   └── Village.php                     # Model Desa / Kelurahan
│   └── Services/
│       └── CattleNikGenerator.php          # Formulasi NIK Sapi Berbasis Kode Wilayah
├── database/
│   ├── data/gorontalo_regions.json         # Data master 6 Kab/Kota, 77 Kec, 722 Desa se-Gorontalo
│   ├── migrations/                         # Skema database terstruktur
│   └── seeders/
│       ├── DatabaseSeeder.php              # Seeder wilayah, akun demo, straw, peternak & sapi
│       └── ReadyToGiveBirthSeeder.php      # Data uji coba sapi bunting siap melahirkan
├── public/
│   ├── manifest.json                       # Konfigurasi Web App Manifest (PWA Ponsel)
│   └── sw.js                               # Service Worker untuk Notifikasi Peramban Ponsel
├── resources/views/                        # Antarmuka Blade UI (Tailwind + Alpine.js)
│   ├── auth/login.blade.php                # Form login petugas/admin
│   ├── calves/                             # Tampilan data anak sapi
│   ├── cows/                               # Direktori sapi, detail kartu digital & QR code
│   ├── farmers/                            # Direktori peternak
│   ├── layouts/app.blade.php               # Layout utama, notifikasi HP, countdown 8 jam & navbar
│   ├── regions/index.blade.php             # Manajemen wilayah & modal edit
│   ├── reports/                            # Leaderboard & rekapan kelahiran
│   ├── straws/index.blade.php              # Database straw pejantan & modal edit
│   ├── users/index.blade.php               # Kelola pengguna & modal edit role
│   ├── dashboard.blade.php                 # Dashboard monitoring interaktif
│   └── home.blade.php                      # Landing page adaptif per-role
├── routes/
│   ├── console.php                         # Jadwal Cronjob (Sesi 8 Jam & Auto-Advance)
│   └── web.php                             # Definisi rute & proteksi middleware
├── PANDUAN_PRESENTASI_SITERNAK.md          # Buku saku penjelasan aplikasi untuk presentasi
└── README.md                               # Dokumentasi resmi aplikasi
```

---

*Dikembangkan untuk **Dinas Peternakan dan Perkebunan Provinsi Gorontalo** — SiTernak-OK.*
