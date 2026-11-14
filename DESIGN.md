# DESIGN.md - SiTernak-OK Design System

> Reading this as: Sistem Informasi Pemerintahan & Operasional Lapangan (GovTech Peternakan) untuk Dinas Peternakan dan Perkebunan Provinsi Gorontalo serta Petugas Inseminator Lapangan, in a Precision Institutional & Field-Ready style, dial ENERGY 2 / RHYTHM 2 / MOTION 1.

Dial: ENERGY 2 / RHYTHM 2 / MOTION 1

## 1. Visual World & Purpose
**SiTernak-OK** adalah sistem pemantauan siklus kebuntingan sapi betina, pencatatan Inseminasi Buatan (IB), rekapitulasi kelahiran pedet, dan pengingat 3 hari untuk **Dinas Peternakan dan Perkebunan Provinsi Gorontalo** (mencakup 6 Kabupaten/Kota).
Aplikasi ini digunakan di dua kondisi nyata:
1. **Lapangan terbuka (siang hari terik melalui peramban ponsel/HP)** oleh Operator Inseminator dengan batas waktu sesi kerja 8 jam.
2. **Ruang kerja dinas (layar desktop/laptop & proyektor presentasi)** oleh Admin Provinsi, Admin Kabupaten/Kota, dan Kepala Dinas.

Oleh karena itu, antarmuka memprioritaskan kontras tinggi (WCAG AA >= 4.5:1), permukaan kartu solid tanpa kabut blur berlebih, tombol sentuh ramah jari di ponsel (`min-h-[44px]`), serta paritas penuh antara Mode Terang (default lapangan) dan Mode Gelap.

## 2. Color System (R-29, R-34)
Tidak menggunakan gradien ungu/violet-ke-indigo khas template AI (`R-01`). Palet warna berakar pada identitas agrikultur dan pemerintahan Provinsi Gorontalo:
- **Surface & Ink (Slate)**:
  - Mode Terang: Latar halaman `#f1f5f9` (`slate-100`), permukaan kartu `#ffffff`, teks utama `#0f172a` (`slate-900`), teks sekunder `#475569` (`slate-600`), garis batas `#e2e8f0` (`slate-200`).
  - Mode Gelap: Latar halaman `#020617` (`slate-950`), permukaan kartu `#0f172a` (`slate-900`), teks utama `#f8fafc` (`slate-50`), teks sekunder `#94a3b8` (`slate-400`), garis batas `#1e293b` (`slate-800`).
- **Primary Brand Accent (Emerald)**: `#059669` (`emerald-600`) dan `#047857` (`emerald-700`) untuk aksi utama, status bunting positif, dan penanda aktif.
- **Secondary Regional / Clinical Accent (Teal & Sky)**: `#0d9488` (`teal-600`) dan `#0284c7` (`sky-600`) untuk navigasi wilayah administratif, kode straw pejantan, dan data pedet.
- **Semantic Alert Accent (Amber & Rose)**:
  - `#d97706` (`amber-600`) khusus untuk jadwal milestone konfirmasi 3 hari, HPL 283 hari, dan hitung mundur sesi kerja 8 jam.
  - `#e11d48` (`rose-600`) khusus untuk aksi destruktif (hapus data, tandai gagal/keguguran, keluar akun).

## 3. Typography & Numerals (R-06, Craft Floor)
- **Primary UI Font**: `Public Sans` (400, 500, 600, 700, 800), font sistem desain pemerintahan (civic/GovTech) dengan keterbacaan tinggi pada teks Bahasa Indonesia baik di layar ponsel lapangan maupun proyektor presentasi.
- **Data & Tabular Monospace**: Font monospace dengan `font-variant-numeric: tabular-nums` digunakan secara terarah untuk:
  - NIK Barcode Sapi (`75.01.01.2001-SAPI-0001`)
  - 16-digit NIK KTP Peternak
  - Kode Straw Pejantan (`LIM-9901`)
  - Hitung mundur sesi kerja 8 jam (`08:00:00`) dan metrik angka tabel.
- **Hierarchy**: Setiap halaman memiliki tepat satu `<h1>` tanpa *eyebrow/kicker pill* dekoratif di atasnya, diikuti `<h2>` dan `<h3>` secara berurutan tanpa melompati level. Tidak menggunakan *gradient text*.

## 4. Surfaces, Borders & Elevation (R-10, R-12, R-13)
- **Glassmorphism Cap (`R-10`)**: `backdrop-filter: blur()` dibatasi maksimal pada 2 elemen fungsional: *sticky header* utama (`.glass-header`) dan latar belakang modal/drawer (`bg-slate-950/75 backdrop-blur-sm`).
- **Solid Cards (`.glass-card`)**: Seluruh kartu konten dan tabel menggunakan latar solid (`#ffffff` di Mode Terang, `#0f172a` di Mode Gelap) dengan border `1px solid` dan bayangan lembut ber-offset (`0 1px 3px 0 rgba(15, 23, 42, 0.06)`).
- **No Thick Side Borders**: Tidak menggunakan `border-l-4` pada kartu metrik; pembeda status dilakukan melalui ikon terstruktur dan badge 1px.

## 5. Motion, Accessibility & Copy Hygiene (R-02, R-19, R-32, R-39)
- **Zoom & Viewport**: `<meta name="viewport" content="width=device-width, initial-scale=1.0">` tanpa `user-scalable=no` atau `maximum-scale=1.0`.
- **Keyboard & Focus**: Tautan *Skip to main content* (`#main-content`), indikator `:focus-visible` `2px solid #10b981` dengan kontras > 3:1, `cursor: pointer` pada seluruh kontrol interaktif, dan `cursor: not-allowed` pada kontrol `disabled`.
- **Motion (`MOTION 1`)**: Transisi halus `150ms-200ms cubic-bezier(0.22, 1, 0.36, 1)` pada hover dan buka-tutup modal. Tidak ada animasi berulang tanpa henti (`animate-ping`, `animate-pulse`, `animate-bounce` dihapus). Menghormati `@media (prefers-reduced-motion: reduce)`.
- **Copywriting (`R-02`)**: Bahasa Indonesia lugas, jelas untuk petugas dinas dan peternak, bebas karakter *em dash* (`—`), dan bebas emoji dekoratif pada teks UI maupun pesan notifikasi.

