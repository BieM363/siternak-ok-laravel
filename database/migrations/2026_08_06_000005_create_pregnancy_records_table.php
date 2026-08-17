<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration untuk tabel pregnancy_records (Riwayat Kebuntingan Sapi)
 * Memuat milestone kebuntingan dan operator_id untuk pembatasan hak akses antar Operator.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pregnancy_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cow_id')->constrained('cows')->onDelete('cascade');
            $table->foreignId('operator_id')->nullable()->constrained('users')->onDelete('set null'); // Isolation Operator A vs B
            $table->date('insemination_date');      // Tanggal Suntik IB (Hari 0)
            $table->string('semen_code')->nullable(); // Kode Straw / Pejantan
            
            // Tanggal Target Milestone
            $table->date('day30_date');             // Tanggal Estimasi 30 Hari pasca IB
            $table->date('day90_date');             // Tanggal Konfirmasi PKB 90 Hari
            $table->date('day245_date');            // Tanggal Reminder Siap Melahirkan (245 Hari)
            $table->date('estimated_birth_date');   // HPL = Inseminasi + 283 Hari
            $table->date('birth_window_end_date');  // Jendela Melahirkan (HPL + 50 Hari / 333 Hari pasca IB)

            $table->date('actual_birth_date')->nullable(); // Realisasi Tanggal Melahirkan
            
            // Status Tahapan Kebuntingan
            $table->enum('status', [
                'Pasca IB',                  // Baru di-IB / Pemantauan Awal (0-30 Hari)
                'Estimasi 30 Hari',          // Menunggu Konfirmasi 30 Hari Pasca IB
                'Bunting (PKB 90 Hari)',     // Terkonfirmasi Bunting / Menunggu PKB 90 Hari
                'Siap Melahirkan (245 Hari)',// Fase 245 Hari Siap Melahirkan
                'Fase Melahirkan',           // HPL & Jendela Melahirkan (+50 hari)
                'Melahirkan (Selesai)',      // Berhasil Melahirkan & Anak Terdata
                'Gagal/Keguguran'            // Tidak Bunting / IB Gagal / Keguguran
            ])->default('Pasca IB');

            $table->string('officer_name')->nullable(); // Nama Inseminator / Petugas Dinas
            $table->timestamp('last_reminder_at')->nullable(); // Waktu Reminder Terakhir Dikirim/Dilihat
            $table->timestamp('confirmed_at')->nullable();     // Tanggal Konfirmasi Manual oleh Operator/Admin
            $table->boolean('is_auto_advanced')->default(false); // Flag Jika Fase Otomatis Berlanjut Karena 3 Hari Tanpa Konfirmasi
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pregnancy_records');
    }
};
