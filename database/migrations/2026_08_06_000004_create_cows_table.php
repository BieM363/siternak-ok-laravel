<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration untuk tabel cows (Data Sapi Betina)
 * Catatan: Kolom gender dihapus karena seluruh sapi dalam sistem ini adalah Betina.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cows', function (Blueprint $table) {
            $table->id();
            $table->string('cow_nik')->unique(); // Formulasi NIK Sapi: [Kode_Kab].[Kode_Kec].[Kode_Desa].[Tahun].[Urut]
            $table->string('name')->nullable();   // Nama Panggilan / Identifier Sapi
            $table->string('breed');              // Limosin, Simental, PO, Brahman, Frisian Holstein, Cross, dll.
            $table->date('birth_date')->nullable();
            $table->foreignId('farmer_id')->constrained('farmers')->onDelete('cascade');
            $table->foreignId('regency_id')->nullable()->constrained('regencies')->onDelete('set null');
            $table->foreignId('district_id')->nullable()->constrained('districts')->onDelete('set null');
            $table->foreignId('village_id')->nullable()->constrained('villages')->onDelete('set null');
            $table->string('current_status')->default('Tidak Bunting'); 
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cows');
    }
};
