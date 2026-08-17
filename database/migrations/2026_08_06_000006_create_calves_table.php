<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration untuk tabel calves (Data Anak Sapi Hasil Kelahiran)
 * Digunakan untuk merekapitulasi kelahiran dan memfilter berdasarkan jenis kelamin anak lahir (Jantan / Betina)
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('calves', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pregnancy_record_id')->constrained('pregnancy_records')->onDelete('cascade');
            $table->foreignId('cow_id')->constrained('cows')->onDelete('cascade'); // Induk Sapi
            $table->string('calf_nik')->unique();   // NIK / Kode Anak Sapi
            $table->string('name')->nullable();       // Nama Anak Sapi
            $table->enum('gender', ['Jantan', 'Betina']); // Jenis Kelamin Anak Lahir
            $table->date('birth_date');             // Tanggal Lahir Anak
            $table->decimal('birth_weight', 5, 2)->nullable(); // Berat Lahir (kg)
            $table->enum('condition', ['Sehat', 'Cacat', 'Mati'])->default('Sehat'); // Kondisi Saat Lahir
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('calves');
    }
};
