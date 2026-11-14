# PRODUCT.md - SiTernak-OK

## Product Overview
**SiTernak-OK** adalah Sistem Informasi Pemantauan Kehamilan Sapi Betina, Pencatatan Inseminasi Buatan (IB), dan Rekapitulasi Kelahiran Pedet untuk **Dinas Peternakan dan Perkebunan Provinsi Gorontalo**.

## Target Audience & Use Context
1. **Operator / Petugas Inseminator Lapangan**: Menggunakan ponsel (peramban HP) saat turun langsung ke kandang peternak di desa/kelurahan. Sesi login dibatasi maksimal 8 jam kerja melalui cronjob otomatis (`ternak:operator-session-cron`) dengan notifikasi langsung ke peramban ponsel.
2. **Admin Kabupaten / Kota**: Mengelola data peternak, wilayah administratif kecamatan dan desa/kelurahan, serta petugas inseminator pada cakupan wilayah masing-masing (Kab. Gorontalo, Kota Gorontalo, Kab. Boalemo, Kab. Bone Bolango, Kab. Pohuwato, Kab. Gorontalo Utara).
3. **Admin Provinsi Gorontalo**: Mengawasi populasi ternak, rekam pelayanan IB, katalog kode straw pejantan, dan laporan kelahiran di seluruh 6 Kabupaten/Kota Provinsi Gorontalo.
4. **Masyarakat Umum / Tamu (Mode Lihat Publik)**: Memantau statistik populasi sapi, siklus kebuntingan, dan peringkat kinerja inseminator secara transparan (NIK KTP peternak disamarkan demi privasi).

