<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration untuk membuat tabel regencies (Kabupaten / Kota)
 * SiTernak-OK (Sistem Pemantauan Kehamilan Sapi)
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('regencies', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique(); // Contoh: "35.07" (Kode Kemendagri Kabupaten Malang)
            $table->string('name');           // Contoh: "Kabupaten Malang", "Kota Batu"
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('regencies');
    }
};
