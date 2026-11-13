<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\File;
use App\Models\User;
use App\Models\Regency;
use App\Models\District;
use App\Models\Village;
use App\Models\Farmer;
use App\Models\Cow;
use App\Models\PregnancyRecord;
use App\Models\BullStraw;
use App\Services\CattleNikGenerator;
use Carbon\Carbon;

class BoalemoInseminationSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('Memulai seeding data IB Kabupaten Boalemo...');

        // 1. Dapatkan Regency Boalemo
        $boalemo = Regency::where('name', 'LIKE', '%Boalemo%')->first();
        if (!$boalemo) {
            $this->command->error('Kabupaten Boalemo tidak ditemukan di database.');
            return;
        }

        // 2. Buat atau dapatkan Petugas Inseminator (Operators)
        $hasman = User::firstOrCreate(
            ['email' => 'hasman.ali@siternak.go.id'],
            [
                'name' => 'HASMAN ALI',
                'password' => Hash::make('password'),
                'role' => 'operator',
                'regency_id' => $boalemo->id,
                'phone' => '081242093301',
            ]
        );

        $yudi = User::firstOrCreate(
            ['email' => 'yudi.prasetyo@siternak.go.id'],
            [
                'name' => 'YUDI PRASETYO',
                'password' => Hash::make('password'),
                'role' => 'operator',
                'regency_id' => $boalemo->id,
                'phone' => '081242093302',
            ]
        );

        $operatorMap = [
            'HASMAN ALI' => $hasman,
            'YUDI PRASETYO' => $yudi,
        ];

        // 3. Pastikan Master Straw Pejantan Lengkap
        $strawTemplates = [
            'BAL-5501' => ['bull_name' => 'Maleo Bali Unggul', 'breed' => 'Bali', 'producer' => 'BIB Daerah Gorontalo'],
            'BRM-9912' => ['bull_name' => 'Perkasa Brahman', 'breed' => 'Brahman', 'producer' => 'BBIB Singosari'],
            'PO-1102'  => ['bull_name' => 'Anoman PO Ongole', 'breed' => 'PO (Peranakan Ongole)', 'producer' => 'BBIB Singosari'],
            'SIM-4421' => ['bull_name' => 'Gatotkaca Simental', 'breed' => 'Simental', 'producer' => 'BBIB Singosari'],
            'LIM-8890' => ['bull_name' => 'Bima Limosin Super', 'breed' => 'Limosin', 'producer' => 'BIB Daerah Gorontalo / BBIB'],
            'ANG-7701' => ['bull_name' => 'Black Angus Bima', 'breed' => 'Angus', 'producer' => 'BBIB Singosari'],
            'WAG-8801' => ['bull_name' => 'Kuroge Wagyu Premium', 'breed' => 'Wagyu', 'producer' => 'BIB Lembang'],
        ];

        foreach ($strawTemplates as $code => $st) {
            BullStraw::firstOrCreate(
                ['code' => $code],
                array_merge($st, ['is_active' => true])
            );
        }

        $breedStrawMap = [
            'Bali'     => 'BAL-5501',
            'Brahman'  => 'BRM-9912',
            'PO'       => 'PO-1102',
            'Simental' => 'SIM-4421',
            'Limousin' => 'LIM-8890',
            'Angus'    => 'ANG-7701',
            'Wagyu'    => 'WAG-8801',
        ];

        // 4. Baca dataset JSON
        $jsonPath = database_path('data/boalemo_inseminations.json');
        if (!File::exists($jsonPath)) {
            $this->command->error("File {$jsonPath} tidak ditemukan!");
            return;
        }

        $data = json_decode(File::get($jsonPath), true);
        $this->command->info('Memproses ' . count($data) . ' baris data Inseminasi Buatan...');

        $farmerSeq = 1001;
        $farmerCache = [];
        $insertedCount = 0;

        foreach ($data as $index => $row) {
            $petugasName = strtoupper(trim($row['petugas']));
            $operator = $operatorMap[$petugasName] ?? $hasman;

            // Resolusi Desa di Kabupaten Boalemo
            $desaRaw = trim($row['desa']);
            $normalizedDesa = match (strtolower($desaRaw)) {
                'jatimulya' => 'Jati Mulya',
                'duluhupa'  => 'Dulohupa',
                'kramat'    => 'Batu Kramat',
                default     => $desaRaw,
            };

            $village = Village::whereHas('district', function ($q) use ($boalemo) {
                $q->where('regency_id', $boalemo->id);
            })->where('name', 'LIKE', "%{$normalizedDesa}%")->first();

            if (!$village) {
                $this->command->warn("Baris #{$index}: Desa {$desaRaw} tidak ditemukan di Boalemo, dilewati.");
                continue;
            }

            $district = $village->district;

            // Resolusi Peternak
            $peternakName = trim($row['peternak']);
            $farmerKey = strtolower($peternakName) . '_' . $village->id;

            if (isset($farmerCache[$farmerKey])) {
                $farmer = $farmerCache[$farmerKey];
            } else {
                $farmer = Farmer::where('name', $peternakName)
                    ->where('village_id', $village->id)
                    ->first();

                if (!$farmer) {
                    $nikDistrict = str_pad((string)($district->id % 100), 2, '0', STR_PAD_LEFT);
                    $farmerNik = '7501' . $nikDistrict . '120585' . str_pad((string)$farmerSeq++, 4, '0', STR_PAD_LEFT);

                    $farmer = Farmer::create([
                        'nik'         => $farmerNik,
                        'name'        => $peternakName,
                        'phone'       => $row['phone'] ?: null,
                        'address'     => 'Desa ' . $village->name . ', Kec. ' . $district->name . ', Kab. Boalemo',
                        'regency_id'  => $boalemo->id,
                        'district_id' => $district->id,
                        'village_id'  => $village->id,
                    ]);
                } elseif ($row['phone'] && empty($farmer->phone)) {
                    $farmer->update(['phone' => $row['phone']]);
                }

                $farmerCache[$farmerKey] = $farmer;
            }

            // Hitung jumlah sapi peternak ini untuk penamaan
            $existingCowsCount = Cow::where('farmer_id', $farmer->id)->count();
            $cowSuffix = $existingCowsCount > 0 ? ' (' . ($existingCowsCount + 1) . ')' : '';
            $cowName = 'Sapi ' . $row['rumpun_akseptor'] . ' - ' . $peternakName . $cowSuffix;

            // Buat NIK Sapi
            $cowNik = CattleNikGenerator::generate($village->id, 2026);

            $ibDate = Carbon::parse($row['tanggal_ib']);
            $today = Carbon::today();
            $diffDays = $ibDate->diffInDays($today);

            // Tentukan status awal berdasarkan durasi kehamilan dari tanggal IB
            if ($diffDays >= 245) {
                $status = 'Siap Melahirkan (245 Hari)';
                $cowStatus = 'Siap Melahirkan';
            } elseif ($diffDays >= 90) {
                $status = 'Bunting (PKB 90 Hari)';
                $cowStatus = 'Bunting';
            } elseif ($diffDays >= 30) {
                $status = 'Estimasi 30 Hari';
                $cowStatus = 'Pasca IB';
            } else {
                $status = 'Pasca IB';
                $cowStatus = 'Pasca IB';
            }

            // Cek apakah sapi serupa sudah pernah diinsert sebelumnya agar aman di-reseed
            $alreadyExists = PregnancyRecord::whereHas('cow', function ($q) use ($farmer) {
                $q->where('farmer_id', $farmer->id);
            })->where('insemination_date', $ibDate->format('Y-m-d'))
              ->where('officer_name', $petugasName)
              ->exists();

            if ($alreadyExists) {
                continue;
            }

            // Simpan Cow
            $cow = Cow::create([
                'cow_nik'        => $cowNik,
                'name'           => $cowName,
                'breed'          => $row['rumpun_akseptor'],
                'birth_date'     => (clone $ibDate)->subYears(3)->subMonths(rand(1, 10))->format('Y-m-d'),
                'farmer_id'      => $farmer->id,
                'regency_id'     => $boalemo->id,
                'district_id'    => $district->id,
                'village_id'     => $village->id,
                'current_status' => $cowStatus,
            ]);

            // Straw pejantan
            $semenCode = $breedStrawMap[$row['rumpun_bull']] ?? 'BAL-5501';

            // Simpan PregnancyRecord
            PregnancyRecord::create([
                'cow_id'                => $cow->id,
                'operator_id'           => $operator->id,
                'officer_name'          => $petugasName,
                'insemination_date'     => $ibDate->format('Y-m-d'),
                'semen_code'            => $semenCode,
                'day30_date'            => (clone $ibDate)->addDays(30)->format('Y-m-d'),
                'day90_date'            => (clone $ibDate)->addDays(90)->format('Y-m-d'),
                'day245_date'           => (clone $ibDate)->addDays(245)->format('Y-m-d'),
                'estimated_birth_date'  => (clone $ibDate)->addDays(283)->format('Y-m-d'),
                'birth_window_end_date' => (clone $ibDate)->addDays(333)->format('Y-m-d'),
                'status'                => $status,
                'notes'                 => 'Pelayanan Inseminasi Buatan (IB) Ke-1 - Boalemo (' . $row['rumpun_bull'] . ')',
            ]);

            $insertedCount++;
        }

        $this->command->info("Selesai! Berhasil menambahkan {$insertedCount} data IB, Sapi, dan Peternak Boalemo.");
    }
}
