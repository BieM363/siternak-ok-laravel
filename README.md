# 🐄 SiTernak-OK — Sistem Pemantauan Kehamilan Sapi (Dinas Peternakan)

Aplikasi berbasis web **Laravel 11** yang dirancang khusus untuk **Dinas Peternakan & Kesehatan Hewan** guna memantau siklus kebuntingan sapi betina, memperhitungkan Hari Perkiraan Lahir (HPL), memberikan pengingat (reminder) berkala, mencatat kelahiran anak sapi (pedet), mengelola master Kode Straw Pejantan IB, serta menyajikan Leaderboard Performa Inseminator.

---

## 📌 Daftar Isi
1. [Fitur Utama & Alur Bisnis](#-fitur-utama--alur-bisnis)
2. [Aturan Siklus Kebuntingan & Reminder 3 Hari](#-aturan-siklus-kebuntingan--reminder-3-hari)
3. [Hak Akses Pengguna & Hierarki Regional (Provinsi vs Kabupaten/Kota)](#-hak-akses-pengguna--hierarki-regional)
4. [Master Kode Straw Pejantan IB & Promosi Pedet Betina](#-master-kode-straw-pejantan-ib--promosi-pedet-betina)
5. [Notifikasi Peramban & Integrasi Google Calendar](#-notifikasi-peramban--integrasi-google-calendar)
6. [Persyaratan Sistem](#-persyaratan-sistem)
7. [Cara Install & Setup Database MySQL XAMPP](#-cara-install--setup-database-mysql-xampp)
8. [Kredensial Akun Login Demo](#-kredensial-akun-login-demo)
9. [Struktur Folder Utama](#-struktur-folder-utama)

---

## 🌟 Fitur Utama & Alur Bisnis

1. **Formulasi NIK Sapi Otomatis (Hierarki Wilayah)**:
   - Format NIK Sapi: `[Kode_Kecamatan].[Kode_Desa].[Tahun].[Nomor_Urut_4_Digit]` (Contoh: `35.07.12.2001.26.0001`).
   - Struktur hirarki wilayah: **Kabupaten/Kota -> Kecamatan -> Desa/Kelurahan**.

2. **Khusus Ternak Sapi Betina**:
   - Seluruh populasi utama adalah sapi betina (induk) yang dipantau kehamilannya.

3. **Master Database Kode Straw Pejantan IB**:
   - Pilihan Kode Straw Pejantan (misal: `LIM-9901`, `SIM-5520`, `ANG-7712`, `PO-1002`) dari BBIB Singosari / Balai Inseminasi yang dapat dipilih via dropdown otomatis saat mendaftarkan sapi induk baru.

4. **Pemisahan Hak Akses Regional (Provinsi vs Kabupaten/Kota & Operator)**:
   - **Admin Provinsi**: Dapat melihat dan mengelola seluruh data sapi, peternak, inseminator, dan wilayah se-provinsi.
   - **Admin Kabupaten/Kota**: Terkunci khusus pada wilayah Kabupaten/Kota masing-masing (misal: Admin Kab. Malang hanya mengelola data Kab. Malang).
   - **Operator / Inseminator**: Memiliki hak menginput sapi induk baru, mengedit data sapi, mengonfirmasi status kebuntingan, menandai sapi gagal/keguguran, serta menghapus data sapi yang gagal.

5. **Dua Menu Terpisah: Leaderboard Inseminator & Rekapan Kelahiran Pedet**:
   - **Leaderboard Inseminator**: Peringkat kinerja inseminator berdasarkan total pelayanan IB, angka kebuntingan positif, pedet lahir, dan nilai *Conception Rate* (CR %).
   - **Rekapan Data Kelahiran Pedet**: Rekapitulasi kelahiran bulanan anak sapi, rincian silsilah induk & peternak, metrik CR & *Calving Rate* (CvR %), serta filter gender pedet.

6. **Promosi Anak Sapi Betina ("Tarik ke Sapi Siap IB")**:
   - Anak sapi (pedet) betina yang sudah lahir dapat dipromosikan langsung menjadi Sapi Induk Siap IB hanya dengan 1-klik, secara otomatis membuatkan NIK dan rekam data sapi betina baru.

7. **Export Laporan PDF & Excel**:
   - Laporan rekapan kelahiran dan performa dapat di-export ke format **PDF (Kop Surat Resmi Dinas)** dan **Excel (CSV)**.

8. **Desain Modern Top Navigation Bar & Mode Terang/Gelap**:
   - Tampilan UI berbasis **Vanilla CSS + Tailwind + Alpine.js** dengan Top Sticky Navigation Bar, kontras terang (Light Mode) yang tinggi, dan tombol aksi tanpa bentrok (`whitespace-nowrap`).

---

## ⏰ Aturan Siklus Kebuntingan & Reminder 3 Hari

Setelah sapi betina menerima suntik **Inseminasi Buatan (IB)**, sistem otomatis menghitung tanggal milestone:

```
[Hari 0] Suntik IB 
   └───► [Hari 30 Pasca IB]   : Estimasi 30 Hari (Konfirmasi Birahi Kembali / Bunting Awal)
   └───► [Hari 90 Pasca IB]   : Konfirmasi Kebuntingan (PKB oleh Dokter/Inseminator)
   └───► [Hari 245 Pasca IB]  : Sapi Bunting Siap Melahirkan (Persiapan Kandang Melahirkan)
   └───► [Hari 283 Pasca IB]  : HPL (Hari Perkiraan Lahir Standard)
   └───► [Jendela 50 Hari]    : Jendela Fase Melahirkan (s/d HPL + 50 Hari / 333 Hari)
```

### ⚡ Otomatisasi Reminder Max 3 Hari & Tombol Manual
* **Aturan**: Jika suatu jadwal konfirmasi (Hari 30, Hari 90, atau Hari 245/Melahirkan) melewati **3 hari tanpa konfirmasi manual** dari Operator, sistem secara **otomatis memajukan fase sapi** ke tahapan berikutnya agar siklus pemantauan tidak terputus.
* **Tombol Check Auto-Advance**: Pengguna/Admin dapat menekan tombol **Check Auto-Advance (3 Hari)** di Dashboard untuk memicu verifikasi milestone secara instan.
* **Perintah Artisan**: `php artisan ternak:update-pregnancy-status` (dapat dipasang pada Cron Job OS).

---

## 👥 Hak Akses Pengguna & Hierarki Regional

Aplikasi mendukung **Multi-level Role & Scope Regional**:

| Role Hak Akses | Scope Wilayah | Hak Akses & Operasional |
| :--- | :--- | :--- |
| 🏛️ **Admin Provinsi** | Seluruh Provinsi (Semua Kab/Kota) | Full Access: Kelola seluruh data sapi, peternak, wilayah, user, master straw, dan laporan. |
| 🏢 **Admin Kabupaten/Kota** | Terkunci 1 Kabupaten/Kota | Kelola data sapi, peternak, kecamatan/desa, dan operator di wilayah Kabupaten/Kota sendiri. |
| 👷 **Operator / Inseminator** | Terkunci Data IB Sendiri / Wilayah | Input Sapi Induk Baru, Konfirmasi YA/TIDAK, Tandai Gagal/Keguguran, Hapus Sapi Gagal, & Tarik Pedet ke Sapi IB. |
| 👁️ **Tamu (Public View)** | Semua Wilayah (Read-Only) | Membaca data populasi sapi, statistik kehamilan, dan laporan tanpa hak modifikasi. |

---

## 🧪 Master Kode Straw Pejantan IB & Promosi Pedet Betina

### 🧬 Master Kode Straw Pejantan (`BullStraw`)
* Pengelolaan master Kode Straw Pejantan IB (misal: `LIM-9901`, `SIM-5520`, `ANG-7712`, `PO-1002`) di menu `/straws`.
* Saat mendaftarkan atau mengedit sapi betina, Operator cukup memilih Kode Straw Pejantan dari dropdown interaktif tanpa mengetik manual.

### 🐄 Promosi Pedet Betina ("Tarik ke Sapi Siap IB")
* Di menu Data Anak Sapi (`/calves`), setiap pedet berjenis kelamin **Betina** memiliki tombol **"Tarik ke Sapi Siap IB"**.
* Mengklik tombol ini otomatis mempromosikan pedet tersebut menjadi sapi betina induk di tabel `cows`, sehingga siap menerima siklus IB berikutnya.

---

## 🔔 Notifikasi Peramban & Integrasi Google Calendar

### 📅 1. Integrasi Google Calendar ("+ Google Calendar")
Setiap rekam kebuntingan sapi dilengkapi tombol **"+ Google Calendar"**.
* Mengklik tombol ini langsung membuka Google Calendar (HP/Desktop) dengan pre-filled Title, Tanggal Event Milestone (30 Hari, 90 Hari, 245 Hari, atau HPL), Detail NIK Sapi, Nama Peternak, dan Lokasi Wilayah Dinas.

### 🔔 2. Browser Notification API (Notifikasi Peramban)
Aplikasi mendukung **Web Notification API**.
* Pengguna dapat mengklik **"Tampilkan Notifikasi HP/Peramban"**.
* Peramban (Chrome, Firefox, Edge, Safari di HP & PC) akan memunculkan popup notifikasi sistem secara otomatis ketika ada pengingat milestone / HPL yang jatuh tempo (Batas Max 3 Hari).

---

## 🛠️ Persyaratan Sistem

- PHP 8.2 atau lebih baru
- Web Server XAMPP / Nginx / Apache
- Database MySQL / MariaDB / SQLite
- Composer package manager

---

## 🚀 Cara Install & Setup Database MySQL XAMPP

1. **Clone repositori dari GitHub**:
   ```bash
   git clone https://github.com/BieM363/siternak-ok-laravel.git
   cd siternak-ok-laravel
   ```

2. **Install dependensi PHP via Composer**:
   ```bash
   composer install
   ```

3. **Pengaturan Berkas `.env`**:
   - Salin `.env.example` menjadi `.env`:
     ```bash
     cp .env.example .env
     ```
   - Konfigurasi database MySQL XAMPP:
     ```env
     DB_CONNECTION=mysql
     DB_HOST=127.0.0.1
     DB_PORT=3306
     DB_DATABASE=siternak_ok
     DB_USERNAME=root
     DB_PASSWORD=
     ```

4. **Jalankan Migration & Seeder**:
   Buat database `siternak_ok` di phpMyAdmin, lalu jalankan:
   ```bash
   php artisan key:generate
   php artisan migrate:fresh --seed
   ```

5. **Jalankan Aplikasi**:
   ```bash
   php artisan serve
   ```
   Akses aplikasi di browser: `http://localhost:8000`.

---

## 🔑 Kredensial Akun Login Demo

Gunakan akun demo berikut untuk menguji isolasi hak akses regional dan role:

1. **Admin Provinsi (Full Akses Seluruh Jawa Timur)**
   - **Email**: `admin.provinsi@siternak.go.id`
   - **Password**: `password`
   - **Fungsi**: Akses dan mengelola seluruh data kabupaten/kota se-provinsi.

2. **Admin Kabupaten Malang**
   - **Email**: `admin.malang@siternak.go.id`
   - **Password**: `password`
   - **Fungsi**: Terkunci mengelola data populasi sapi & petugas di Kab. Malang.

3. **Admin Kabupaten Pasuruan**
   - **Email**: `admin.pasuruan@siternak.go.id`
   - **Password**: `password`
   - **Fungsi**: Terkunci mengelola data populasi sapi & petugas di Kab. Pasuruan.

4. **Operator / Inseminator (Budi Inseminator)**
   - **Email**: `operator@siternak.go.id`
   - **Password**: `password`
   - **Fungsi**: Menginput sapi betina baru, memilih Kode Straw, konfirmasi YA/TIDAK, tandai gagal, dan hapus sapi gagal.

5. **Tamu (Public / Read-Only)**
   - **Email**: `tamu@siternak.go.id`
   - **Password**: `password`
   - **Fungsi**: Membaca data populasi sapi, leaderboard inseminator, dan laporan tanpa hak aksi modifikasi.

---

## 📂 Struktur Folder Utama

```
siternak-ok-laravel/
├── app/
│   ├── Console/Commands/
│   │   └── AutoAdvancePregnancyPhases.php  # Command otomatisasi 3 hari tanpa konfirmasi
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── AuthController.php
│   │   │   ├── BullStrawController.php    # Master Kode Straw Pejantan
│   │   │   ├── CalfController.php          # Data Anak Sapi & Promosi Pedet Betina
│   │   │   ├── CowController.php           # Scoping data per Operator & Regional
│   │   │   ├── DashboardController.php     # Dashboard stats, auto-advance, & reminder
│   │   │   ├── FarmerController.php        # Data Peternak (NIK KTP & Nama)
│   │   │   ├── PregnancyController.php    # Logika konfirmasi YA/TIDAK & input pedet
│   │   │   ├── RegionController.php       # Hirarki Kabupaten, Kecamatan, Desa
│   │   │   ├── ReportController.php       # Leaderboard Inseminator & Rekapan Kelahiran
│   │   │   └── UserController.php         # Manajemen Pengguna & Scope Regional
│   │   └── Middleware/
│   │       └── RoleMiddleware.php         # Middleware proteksi role & regional
│   ├── Models/
│   │   ├── BullStraw.php                  # Model Master Kode Straw Pejantan
│   │   ├── Calf.php                       # Model Anak Sapi (Jantan/Betina) & Promosi
│   │   ├── Cow.php                        # Model Sapi Betina & scopeForOperator
│   │   ├── District.php                   # Model Kecamatan
│   │   ├── Farmer.php                     # Model Peternak
│   │   ├── PregnancyRecord.php            # Model Pemantauan Kebuntingan & Google Calendar URL
│   │   ├── Regency.php                    # Model Kabupaten/Kota
│   │   ├── User.php                       # Model User, Admin Provinsi, Admin Kab, Operator
│   │   └── Village.php                    # Model Desa
│   └── Services/
│       └── CattleNikGenerator.php         # Service Generator NIK Sapi
├── database/
│   ├── migrations/                        # Skema tabel database (termasuk bull_straws & regency_id)
│   └── seeders/DatabaseSeeder.php         # Seeder data awal & demo regional
├── resources/views/                       # Seluruh tampilan Blade UI (Top Nav, Light/Dark Mode)
├── routes/web.php                         # Rute web & pembatasan middleware
└── README.md                              # Dokumentasi resmi aplikasi
```

---
*Dibuat untuk Dinas Peternakan & Kesehatan Hewan — SiTernak-OK.*
