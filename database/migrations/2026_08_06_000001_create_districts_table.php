<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration untuk membuat tabel districts (Kecamatan)
 * Terhubung ke regencies (Kabupaten / Kota)
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('districts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('regency_id')->nullable()->constrained('regencies')->onDelete('cascade');
            $table->string('code')->unique(); // Contoh: "35.07.12" (Kode Kecamatan Kepanjen)
            $table->string('name');           // Contoh: "Kepanjen", "Singosari"
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('districts');
    }
};
