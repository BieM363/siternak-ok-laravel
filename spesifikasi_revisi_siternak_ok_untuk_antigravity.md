# Spesifikasi Revisi Sistem Pemantauan Kebuntingan Sapi (SiTernak OK)

Dokumen ini berisi daftar revisi teknis berdasarkan hasil rapat bersama Kepala Dinas Peternakan dan Perkebunan Provinsi Gorontalo untuk diimplementasikan pada proyek Laravel `siternak-ok-laravel`.

---

## 1. Penyesuaian Istilah & UI/UX
- **Ganti Istilah Global:**
  - Ubah semua kata **"Kehamilan"** / **"Hamil"** menjadi **"Kebuntingan"** / **"Bunting"** (di UI, label, pesan notifikasi, dan judul halaman).
  - Ubah nama role **"Operator Lapangan"** menjadi **"Inseminator"**.
  - Ubah label **"Tanggal Suntik IB"** menjadi **"Tanggal Inseminasi"**.
- **Penyederhanaan Tampilan Inseminator (Mobile-First):**
  - Buat tampilan untuk role `Inseminator` sesederhana dan seringan mungkin untuk diakses lewat HP Android. Fokuskan halaman utamanya langsung pada form input laporan IB dan riwayat inputan pribadinya tanpa menu analitik/tabel yang berat.
- **Fitur yang Dibatalkan (Jangan Dibuat):**
  - **Jangan** menambahkan fitur unggah foto dan titik koordinat GPS pada form input agar aplikasi tetap ringan bagi petugas lapangan dan server.

---

## 2. Manajemen Role & Hak Akses
Sederhanakan manajemen akun menjadi **3 role utama** (dengan **2 jenis admin saja**):

1. **Admin Provinsi:**
   - Akses penuh melihat dan merekap data seluruh kabupaten/kota se-Provinsi Gorontalo.
   - Mengelola data wilayah dan master data straw (distribusi dari provinsi).
2. **Admin Kabupaten/Kota (Recorder Dinas):**
   - Cukup sediakan **1 halaman/pintu login** untuk Admin Kabupaten/Kota (tidak perlu tombol login terpisah per daerah).
   - Data otomatis terfilter sesuai `kabupaten_id` akun yang login (tidak bisa melihat data kabupaten lain).
   - Menerima notifikasi pengecekan kebuntingan untuk dikonfirmasi ke inseminator, lalu mengupdate statusnya di sistem.
   - Dapat membantu menginputkan laporan IB secara susulan (*backdate*) satu per satu bagi inseminator lapangan yang mencatat manual.
3. **Inseminator:**
   - Dapat memilih wilayah (Kabupaten, Kecamatan, Desa) secara bebas saat menginput laporan untuk mengakomodasi petugas yang melayani lintas kabupaten maupun inseminator mandiri.

---

## 3. Revisi Skema Database & Form Input

### A. Data Pemilik Sapi (Peternak)
- **Input Manual (Custom):** Pada form input IB, petugas bisa memilih peternak yang sudah ada di database **atau** mengetik manual nama peternak baru (opsi custom).
- **NIK Opsional:** Ubah kolom `nik` pada tabel peternak/pemilik menjadi **`nullable`** (tidak wajib diisi dan hilangkan tanda bintang `*` di form).
- **Filter Tanpa NIK:** Tambahkan fitur filter pada daftar pemilik sapi / laporan untuk menyaring data pemilik yang tidak memiliki NIK.
- **Kepemilikan Banyak Sapi:** Pastikan relasi database mendukung **1 peternak memiliki banyak sapi** (`one-to-many`).

### B. Data Sapi Betina (Akseptor)
- **Dropdown Ras/Jenis Sapi Lengkap:** Lengkapi pilihan *dropdown* jenis sapi (misal: Limousin, Simental, Bali, Peranakan Ongole/PO, Friesian Holstein/FH, Brahman, Angus, Lokal, dll.) agar petugas cukup memilih tanpa mengetik.
- **Nama Sapi Otomatis Jika Kosong:** Kolom Nama Panggilan Sapi tetap dipertahankan untuk membedakan jika 1 peternak punya banyak sapi. Jika petugas mengosongkannya saat input, sistem otomatis mengisi nama dengan pola `[Nama Pemilik] 1`, `[Nama Pemilik] 2`, dst.

### C. Identitas Pejantan (Straw)
- Ganti kolom tunggal kode straw menjadi grup **Identitas Pejantan** yang terdiri dari 3 kolom utama:
  1. **Rumpun** (jenis ras pejantan)
  2. **Kode Bull**
  3. **Kode Batch**
  *(Opsional: pertahankan kolom Produsen/Sumber seperti BBIB Singosari / BIB Lembang jika sudah ada).*
- **Opsi Dropdown & Custom:** Petugas bisa memilih straw dari master data yang diinput Admin Provinsi, **atau** memilih opsi **Custom** untuk mengetik manual (mengantisipasi stok straw tahun lama atau straw mandiri).

### D. Tanggal Inseminasi & Tanggal Pelaporan
- **Backdate Tanggal Inseminasi:** Field `tanggal_inseminasi` harus mengizinkan pemilihan tanggal mundur (di bawah tanggal hari ini) untuk input susulan.
- **Pisahkan Tanggal Pelaksanaan & Input:** Bedakan `tanggal_inseminasi` (tanggal pelaksanaan di lapangan) dengan `created_at` (tanggal pelaporan masuk ke sistem).
- **Basis Perhitungan:** Seluruh estimasi jadwal (30 hari, 90 hari, 9 bulan) dan pemicu notifikasi **wajib dihitung dari `tanggal_inseminasi`**, bukan dari `created_at`.

---

## 4. Alur IB Berulang (IB ke-2, dst.) & Logika Notifikasi

### A. Pencatatan IB ke-2 dan Seterusnya
- Tambahkan kolom/pencatatan urutan IB (misal: `ib_ke` bernilai 1, 2, 3, dst. atau tabel riwayat inseminasi yang berelasi dengan `sapi_id`).
- Jika IB ke-1 dinyatakan **Gagal / Tidak Bunting** (atau sapi sudah melahirkan dan akan di-IB lagi), petugas tidak perlu menginput ulang data peternak dan data sapi.
- Sediakan tombol/opsi **"Input IB Lanjutan (IB ke-2, dst.)"** langsung pada data sapi yang sudah ada, sehingga data peternak dan sapi otomatis terisi dan petugas cukup mengisi tanggal inseminasi serta identitas pejantan terbaru.

### B. Sistem Notifikasi 3 Tahap (Ke Admin Kabupaten/Kota)
Arahkan notifikasi pengecekan status ke dashboard **Admin Kabupaten/Kota** pada 3 titik waktu (dihitung dari `tanggal_inseminasi`):
1. **Tahap 1 — Hari ke-30:** Filter awal (konfirmasi apakah gagal/minta kawin ulang atau lanjut).
2. **Tahap 2 — Hari ke-90 (3 Bulan):** Pemeriksaan Kebuntingan (PKB) pasti (konfirmasi Bunting / Tidak Bunting).
3. **Tahap 3 — Bulan ke-9 (~270–280 Hari):** Konfirmasi Kelahiran (jika Ya -> otomatis masuk ke data Pedet & Kelahiran).

- **Aturan Auto-Lanjut:** Jika notifikasi pengecekan tidak direspons oleh Admin Kabupaten/Kota dalam batas waktu **3 hari**, sistem otomatis mengasumsikan status kebuntingan **terus berlanjut** ke tahap berikutnya.
- **Aturan Gagal:** Jika dikonfirmasi **Tidak Bunting / Gagal**, status sapi keluar dari hitungan kebuntingan aktif dan siap masuk ke alur IB ke-2.

---

## 5. Menu Laporan, Rekapitulasi & Ekspor Data

1. **Menu Khusus "Data Kebuntingan":**
   - Buat menu dan halaman tersendiri untuk **Data Kebuntingan** (terpisah dari daftar semua Sapi IB, dibuat mirip struktur halaman Pedet).
   - Tampilkan jumlah total sapi bunting, jenis/rumpun straw, lokasi, pemilik, serta **umur kebuntingan (dalam bulan)** yang terhitung otomatis dari `tanggal_inseminasi`.
2. **Gabungkan Menu "Pedet" & "Kelahiran":**
   - Satukan menu `Pedet` dan `Kelahiran` menjadi satu menu terpadu agar tidak ada duplikasi navigasi.
3. **Rekapitulasi & Filter Kinerja:**
   - Pada dashboard/halaman laporan, tampilkan angka rekap: **Total Sapi di-IB**, **Total Bunting**, dan **Total Kelahiran/Pedet** (terinci berdasarkan ras/jenis sapi).
   - Sediakan filter berdasarkan:
     - **Kabupaten/Kota**
     - **Nama Petugas Inseminator** (wajib untuk evaluasi kinerja dan penilaian lomba inseminator terbaik)
     - **Periode Tanggal**
     - **Status NIK Pemilik** (ada NIK / tanpa NIK)
4. **Ekspor ke Excel:**
   - Tambahkan fitur ekspor laporan dan rekapitulasi ke format **Excel (`.xlsx`)** (misal menggunakan `maatwebsite/excel`), melengkapi fitur ekspor PDF yang sudah ada. Output ekspor harus mengikuti filter yang sedang aktif (per kabupaten/per inseminator/per periode).

---

## 6. Saran Urutan Pengerjaan di Antigravity
1. **Database & Migrations:** Update migrasi untuk `nik` nullable, pemisahan kolom pejantan (`rumpun`, `kode_bull`, `kode_batch`), dukungan `ib_ke` / riwayat IB, dan penyederhanaan role.
2. **Models & Business Logic:** Update relasi (1 peternak -> banyak sapi -> banyak riwayat IB), fungsi auto-generate nama sapi jika kosong, perhitungan umur kebuntingan (bulan), dan logika 3 tahap notifikasi + auto-lanjut 3 hari berbasis `tanggal_inseminasi`.
3. **Controllers & Middleware:** Terapkan pembatasan scope data per `kabupaten_id` untuk Admin Kabupaten/Kota, endpoint input custom peternak & straw, serta alur klik IB ke-2.
4. **Views & Blade UI:** Ganti seluruh istilah, sederhanakan form mobile Inseminator, buat menu Data Kebuntingan, gabungkan menu Pedet & Kelahiran, dan tambahkan filter laporan.
5. **Export Feature:** Implementasikan ekspor Excel (`.xlsx`) untuk rekap IB, Kebuntingan, dan Pedet/Kelahiran.