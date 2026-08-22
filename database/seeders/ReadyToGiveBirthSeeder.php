<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ReadyToGiveBirthSeeder extends Seeder
{
    public function run(): void
    {
        /*
        |--------------------------------------------------------------------------
        | DATA YANG SUDAH KITA KETAHUI DARI DATABASE
        |--------------------------------------------------------------------------
        */

        // Operator yang sudah ada
        $operator = DB::table('users')
            ->where('role', 'operator')
            ->first();

        if (!$operator) {
            $this->command->error('Tidak ditemukan user dengan role operator.');
            return;
        }

        // Ambil beberapa peternak yang sudah ada
        $farmers = DB::table('farmers')
            ->orderBy('id')
            ->take(5)
            ->get();

        if ($farmers->count() < 5) {
            $this->command->error('Data peternak kurang dari 5.');
            return;
        }

        // Kabupaten yang sudah ada
        $regency = DB::table('regencies')
            ->first();

        if (!$regency) {
            $this->command->error('Data kabupaten tidak ditemukan.');
            return;
        }

        // Kecamatan dari kabupaten tersebut
        $district = DB::table('districts')
            ->where('regency_id', $regency->id)
            ->first();

        if (!$district) {
            $this->command->error('Data kecamatan tidak ditemukan.');
            return;
        }

        // Desa dari kecamatan tersebut
        $village = DB::table('villages')
            ->where('district_id', $district->id)
            ->first();

        if (!$village) {
            $this->command->error('Data desa tidak ditemukan.');
            return;
        }

        /*
        |--------------------------------------------------------------------------
        | DATA SAPI DUMMY
        |--------------------------------------------------------------------------
        */

        $cows = [
            [
                'cow_nik' => 'DUMMY-READY-001',
                'name' => 'Sapi Dummy 1',
                'breed' => 'Limousin',
            ],
            [
                'cow_nik' => 'DUMMY-READY-002',
                'name' => 'Sapi Dummy 2',
                'breed' => 'Simmental',
            ],
            [
                'cow_nik' => 'DUMMY-READY-003',
                'name' => 'Sapi Dummy 3',
                'breed' => 'Limousin',
            ],
            [
                'cow_nik' => 'DUMMY-READY-004',
                'name' => 'Sapi Dummy 4',
                'breed' => 'Simmental',
            ],
            [
                'cow_nik' => 'DUMMY-READY-005',
                'name' => 'Sapi Dummy 5',
                'breed' => 'Limousin',
            ],
        ];

        foreach ($cows as $index => $cowData) {

            /*
            |--------------------------------------------------------------------------
            | HAPUS DATA DUMMY JIKA SEBELUMNYA SUDAH PERNAH DIBUAT
            |--------------------------------------------------------------------------
            */

            $oldCow = DB::table('cows')
                ->where('cow_nik', $cowData['cow_nik'])
                ->first();

            if ($oldCow) {

                DB::table('pregnancy_records')
                    ->where('cow_id', $oldCow->id)
                    ->delete();

                DB::table('cows')
                    ->where('id', $oldCow->id)
                    ->delete();
            }

            /*
            |--------------------------------------------------------------------------
            | BUAT SAPI
            |--------------------------------------------------------------------------
            */

            $cowId = DB::table('cows')->insertGetId([
                'cow_nik' => $cowData['cow_nik'],
                'name' => $cowData['name'],
                'breed' => $cowData['breed'],
                'birth_date' => Carbon::now()->subYears(4)->format('Y-m-d'),
                'farmer_id' => $farmers[$index]->id,
                'regency_id' => $regency->id,
                'district_id' => $district->id,
                'village_id' => $village->id,
                'current_status' => 'Mendekati Melahirkan',
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            /*
            |--------------------------------------------------------------------------
            | TANGGAL DUMMY
            |--------------------------------------------------------------------------
            |
            | Kita buat sapi berada pada:
            | Siap Melahirkan (245 Hari)
            |
            | Dan HPL = sekitar hari ini.
            |
            */

            $inseminationDate = Carbon::now()
                ->subDays(283);

            $day30 = $inseminationDate
                ->copy()
                ->addDays(30);

            $day90 = $inseminationDate
                ->copy()
                ->addDays(90);

            $day245 = $inseminationDate
                ->copy()
                ->addDays(245);

            $estimatedBirth = $inseminationDate
                ->copy()
                ->addDays(283);

            $birthWindowEnd = $estimatedBirth
                ->copy()
                ->addDays(3);

            /*
            |--------------------------------------------------------------------------
            | BUAT RECORD KEBUNTINGAN
            |--------------------------------------------------------------------------
            */

            DB::table('pregnancy_records')->insert([
                'cow_id' => $cowId,

                'operator_id' => $operator->id,

                'insemination_date' => $inseminationDate->format('Y-m-d'),

                'semen_code' => 'DUMMY-SEMENT-001',

                'day30_date' => $day30->format('Y-m-d'),

                'day90_date' => $day90->format('Y-m-d'),

                'day245_date' => $day245->format('Y-m-d'),

                'estimated_birth_date' => $estimatedBirth->format('Y-m-d'),

                'birth_window_end_date' => $birthWindowEnd->format('Y-m-d'),

                'actual_birth_date' => null,

                /*
                |--------------------------------------------------------------------------
                | STATUS PALING PENTING
                |--------------------------------------------------------------------------
                |
                | Ini yang membuat popup:
                |
                | "Konfirmasi Kelahiran"
                |
                */

                'status' => 'Fase Melahirkan',

                'officer_name' => $operator->name,

                'last_reminder_at' => null,

                'confirmed_at' => null,

                'is_auto_advanced' => false,

                'notes' => 'DATA DUMMY UNTUK TEST POPUP KONFIRMASI KELAHIRAN',

                'created_at' => now(),

                'updated_at' => now(),
            ]);

            $this->command->info(
                'Berhasil membuat: ' . $cowData['name']
            );
        }

        $this->command->newLine();

        $this->command->info(
            '=============================================='
        );

        $this->command->info(
            '5 DATA DUMMY SIAP MELAHIRKAN BERHASIL DIBUAT'
        );

        $this->command->info(
            '=============================================='
        );
    }
}