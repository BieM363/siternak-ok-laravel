<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('calves', function (Blueprint $table) {
            $table->string('breed')->nullable()->after('name');
            $table->boolean('is_promoted')->default(false)->after('notes');
            $table->foreignId('promoted_cow_id')
                ->nullable()
                ->after('is_promoted')
                ->constrained('cows')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('calves', function (Blueprint $table) {
            $table->dropForeign(['promoted_cow_id']);
            $table->dropColumn(['breed', 'is_promoted', 'promoted_cow_id']);
        });
    }
};
