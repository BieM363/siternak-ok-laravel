<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration untuk tabel farmers (Data Peternak)
 * Menyimpan NIK dan Nama Peternak secara terpisah.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('farmers', function (Blueprint $table) {
            $table->id();
            $table->string('nik')->unique();   // NIK KTP Peternak (16 Digit)
            $table->string('name');            // Nama Lengkap Peternak
            $table->string('phone')->nullable();
            $table->text('address')->nullable();
            $table->foreignId('regency_id')->nullable()->constrained('regencies')->onDelete('set null');
            $table->foreignId('district_id')->nullable()->constrained('districts')->onDelete('set null');
            $table->foreignId('village_id')->nullable()->constrained('villages')->onDelete('set null');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('farmers');
    }
};
