<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Migration untuk tabel bull_straws (Database Kode Straw Pejantan Inseminasi Buatan)
     */
    public function up(): void
    {
        Schema::create('bull_straws', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique(); // Contoh: LIM-8890, SIM-4421, FH-7731
            $table->string('bull_name');     // Nama Pejantan, misal: Bima Limosin
            $table->string('breed');         // Ras / Rumpun Pejantan (Limosin, Simental, FH, PO, Brahman, Madura)
            $table->string('producer')->nullable()->default('BBIB Singosari'); // Produsen / Balai IB
            $table->boolean('is_active')->default(true); // Status Aktif Straw
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bull_straws');
    }
};
