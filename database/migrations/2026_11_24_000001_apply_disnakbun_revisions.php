<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Ubah kolom nik pada tabel farmers menjadi nullable (opsional)
        Schema::table('farmers', function (Blueprint $table) {
            $table->string('nik')->nullable()->change();
        });

        // 2. Lengkapi tabel bull_straws dengan kode_bull dan kode_batch (Identitas Pejantan 3 kolom: breed/Rumpun, kode_bull, kode_batch)
        Schema::table('bull_straws', function (Blueprint $table) {
            if (!Schema::hasColumn('bull_straws', 'kode_bull')) {
                $table->string('kode_bull', 60)->nullable()->after('code');
            }
            if (!Schema::hasColumn('bull_straws', 'kode_batch')) {
                $table->string('kode_batch', 60)->nullable()->after('kode_bull');
            }
        });

        // Backfill kode_bull & kode_batch dari data straw lama
        DB::table('bull_straws')->whereNull('kode_bull')->get()->each(function ($straw) {
            DB::table('bull_straws')->where('id', $straw->id)->update([
                'kode_bull' => $straw->code,
                'kode_batch' => 'BATCH-' . date('Y') . '-' . str_pad((string) $straw->id, 2, '0', STR_PAD_LEFT),
            ]);
        });

        // 3. Tambahkan kolom IB berulang (ib_ke), Identitas Pejantan (3 kolom + custom), dan target Bulan ke-9 (day270_date)
        Schema::table('pregnancy_records', function (Blueprint $table) {
            if (!Schema::hasColumn('pregnancy_records', 'ib_ke')) {
                $table->unsignedSmallInteger('ib_ke')->default(1)->after('operator_id');
            }
            if (!Schema::hasColumn('pregnancy_records', 'bull_straw_id')) {
                $table->foreignId('bull_straw_id')->nullable()->after('semen_code')->constrained('bull_straws')->nullOnDelete();
            }
            if (!Schema::hasColumn('pregnancy_records', 'straw_rumpun')) {
                $table->string('straw_rumpun', 80)->nullable()->after('bull_straw_id');
            }
            if (!Schema::hasColumn('pregnancy_records', 'straw_kode_bull')) {
                $table->string('straw_kode_bull', 80)->nullable()->after('straw_rumpun');
            }
            if (!Schema::hasColumn('pregnancy_records', 'straw_kode_batch')) {
                $table->string('straw_kode_batch', 80)->nullable()->after('straw_kode_bull');
            }
            if (!Schema::hasColumn('pregnancy_records', 'straw_producer')) {
                $table->string('straw_producer', 100)->nullable()->after('straw_kode_batch');
            }
            if (!Schema::hasColumn('pregnancy_records', 'is_custom_straw')) {
                $table->boolean('is_custom_straw')->default(false)->after('straw_producer');
            }
            if (!Schema::hasColumn('pregnancy_records', 'day270_date')) {
                $table->date('day270_date')->nullable()->after('day245_date');
            }
        });

        // Backfill pregnancy_records lama (urutan ib_ke, identitas pejantan, dan day270_date)
        $strawMap = DB::table('bull_straws')->get()->keyBy('code');
        $cowCounters = [];

        DB::table('pregnancy_records')
            ->orderBy('cow_id')
            ->orderBy('insemination_date')
            ->orderBy('id')
            ->get()
            ->each(function ($rec) use (&$cowCounters, $strawMap) {
                $cowCounters[$rec->cow_id] = ($cowCounters[$rec->cow_id] ?? 0) + 1;
                $matchedStraw = $rec->semen_code ? ($strawMap[$rec->semen_code] ?? null) : null;
                $day270 = $rec->insemination_date
                    ? \Carbon\Carbon::parse($rec->insemination_date)->addDays(270)->format('Y-m-d')
                    : null;

                DB::table('pregnancy_records')->where('id', $rec->id)->update([
                    'ib_ke' => $cowCounters[$rec->cow_id],
                    'bull_straw_id' => $matchedStraw->id ?? null,
                    'straw_rumpun' => $rec->straw_rumpun ?: ($matchedStraw->breed ?? null),
                    'straw_kode_bull' => $rec->straw_kode_bull ?: ($matchedStraw->kode_bull ?? $rec->semen_code),
                    'straw_kode_batch' => $rec->straw_kode_batch ?: ($matchedStraw->kode_batch ?? null),
                    'straw_producer' => $rec->straw_producer ?: ($matchedStraw->producer ?? null),
                    'day270_date' => $rec->day270_date ?: $day270,
                ]);
            });
    }

    public function down(): void
    {
        Schema::table('pregnancy_records', function (Blueprint $table) {
            $cols = ['ib_ke', 'straw_rumpun', 'straw_kode_bull', 'straw_kode_batch', 'straw_producer', 'is_custom_straw', 'day270_date'];
            if (Schema::hasColumn('pregnancy_records', 'bull_straw_id')) {
                $table->dropConstrainedForeignId('bull_straw_id');
            }
            foreach ($cols as $col) {
                if (Schema::hasColumn('pregnancy_records', $col)) {
                    $table->dropColumn($col);
                }
            }
        });

        Schema::table('bull_straws', function (Blueprint $table) {
            foreach (['kode_bull', 'kode_batch'] as $col) {
                if (Schema::hasColumn('bull_straws', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};
