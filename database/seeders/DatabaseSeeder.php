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
use App\Models\Calf;
use App\Models\BullStraw;
use App\Services\CattleNikGenerator;
use Carbon\Carbon;

/**
 * DatabaseSeeder
 * SiTernak-OK (Sistem Pemantauan Kehamilan Sapi - Dinas Peternakan dan Perkebunan Provinsi Gorontalo)
 * Mengisi data seluruh Wilayah Gorontalo (6 Kab/Kota, 77 Kec, 722 Desa), Admin Wilayah, Petugas IB, Master Straw, Peternak, dan Sapi.
 */
class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('1. Memuat seluruh data wilayah Provinsi Gorontalo...');
        
        $jsonPath = database_path('data/gorontalo_regions.json');
        if (!File::exists($jsonPath)) {
            $this->command->warn('File gorontalo_regions.json belum ada, memicu scraping...');
            \Illuminate\Support\Facades\Artisan::call('regions:scrape-gorontalo');
        }

        $regionsData = json_decode(File::get($jsonPath), true) ?: [];

        $regencyMap = [];
        $districtMap = [];
        $villageList = [];

        foreach ($regionsData as $rData) {
            $regency = Regency::create([
                'code' => $rData['code'],
                'name' => $rData['name'],
            ]);
            $regencyMap[$rData['code']] = $regency;

            foreach ($rData['districts'] as $dData) {
                $district = District::create([
                    'regency_id' => $regency->id,
                    'code' => $dData['code'],
                    'name' => $dData['name'],
                ]);
                $districtMap[$dData['code']] = $district;

                foreach ($dData['villages'] as $vData) {
                    $village = Village::create([
                        'district_id' => $district->id,
                        'code' => $vData['code'],
                        'name' => $vData['name'],
                    ]);
                    $villageList[] = $village;
                }
            }
        }

        $this->command->info('   -> Berhasil memuat ' . count($regencyMap) . ' Kab/Kota, ' . count($districtMap) . ' Kecamatan, ' . count($villageList) . ' Desa/Kelurahan.');

        // 2. Buat User Hak Akses (Admin Provinsi Gorontalo, Admin Tiap Kabupaten/Kota, Operator/Inseminator)
        $this->command->info('2. Membuat data pengguna & hak akses Gorontalo...');

        $adminProvinsi = User::create([
            'name' => 'Admin Dinas Peternakan & Perkebunan Prov. Gorontalo',
            'email' => 'admin@siternak.go.id',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'regency_id' => null, // Akses Semua Kabupaten/Kota
            'phone' => '081234567890',
        ]);

        $regGorontalo = $regencyMap['75.02'] ?? array_values($regencyMap)[0];
        $regKotaGorontalo = $regencyMap['75.71'] ?? array_values($regencyMap)[0];
        $regBoalemo = $regencyMap['75.01'] ?? array_values($regencyMap)[0];
        $regBoneBolango = $regencyMap['75.04'] ?? array_values($regencyMap)[0];
        $regPohuwato = $regencyMap['75.03'] ?? array_values($regencyMap)[0];
        $regGorut = $regencyMap['75.05'] ?? array_values($regencyMap)[0];

        $adminKabGorontalo = User::create([
            'name' => 'Admin Kabupaten Gorontalo',
            'email' => 'admin.gorontalo@siternak.go.id',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'regency_id' => $regGorontalo->id,
            'phone' => '081299887766',
        ]);

        $adminKotaGorontalo = User::create([
            'name' => 'Admin Kota Gorontalo',
            'email' => 'admin.kotagorontalo@siternak.go.id',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'regency_id' => $regKotaGorontalo->id,
            'phone' => '081377665544',
        ]);

        $adminBoalemo = User::create([
            'name' => 'Admin Kabupaten Boalemo',
            'email' => 'admin.boalemo@siternak.go.id',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'regency_id' => $regBoalemo->id,
            'phone' => '081344556677',
        ]);

        $opA = User::create([
            'name' => 'Operator A (Budi Inseminator - Gorontalo)',
            'email' => 'operator@siternak.go.id',
            'password' => Hash::make('password'),
            'role' => 'operator',
            'regency_id' => $regGorontalo->id,
            'phone' => '089876543210',
        ]);

        $opB = User::create([
            'name' => 'Operator B (M. Taufik Inseminator - Gorontalo)',
            'email' => 'operator2@siternak.go.id',
            'password' => Hash::make('password'),
            'role' => 'operator',
            'regency_id' => $regGorontalo->id,
            'phone' => '087711223344',
        ]);

        // 3. Database Master Kode Straw Pejantan IB
        $this->command->info('3. Membuat master data Straw Pejantan IB...');
        $straws = [
            ['code' => 'LIM-8890', 'bull_name' => 'Bima Limosin Super', 'breed' => 'Limosin', 'producer' => 'BIB Daerah Gorontalo / BBIB'],
            ['code' => 'SIM-4421', 'bull_name' => 'Gatotkaca Simental', 'breed' => 'Simental', 'producer' => 'BBIB Singosari'],
            ['code' => 'FH-7731', 'bull_name' => 'Subali FH Perah', 'breed' => 'Frisian Holstein', 'producer' => 'BIB Lembang'],
            ['code' => 'PO-1102', 'bull_name' => 'Anoman PO Ongole', 'breed' => 'PO (Peranakan Ongole)', 'producer' => 'BBIB Singosari'],
            ['code' => 'BRM-9912', 'bull_name' => 'Perkasa Brahman', 'breed' => 'Brahman', 'producer' => 'BBIB Singosari'],
            ['code' => 'LIM-3320', 'bull_name' => 'Super Limosin II', 'breed' => 'Limosin', 'producer' => 'BBIB Singosari'],
            ['code' => 'SIM-9001', 'bull_name' => 'Megantara Simental', 'breed' => 'Simental', 'producer' => 'BBIB Singosari'],
            ['code' => 'BAL-5501', 'bull_name' => 'Maleo Bali Unggul', 'breed' => 'Bali', 'producer' => 'BIB Daerah Gorontalo'],
        ];

        foreach ($straws as $st) {
            BullStraw::create(array_merge($st, ['is_active' => true]));
        }

        // 4. Buat Data Peternak di Wilayah Gorontalo
        $this->command->info('4. Membuat data peternak Gorontalo...');
        $vGorontalo = Village::whereHas('district', function($q) use ($regGorontalo) {
            $q->where('regency_id', $regGorontalo->id);
        })->take(4)->get();

        $vKota = Village::whereHas('district', function($q) use ($regKotaGorontalo) {
            $q->where('regency_id', $regKotaGorontalo->id);
        })->first() ?: $villageList[0];

        $vBoalemo = Village::whereHas('district', function($q) use ($regBoalemo) {
            $q->where('regency_id', $regBoalemo->id);
        })->first() ?: $villageList[0];

        $farmers = [
            ['nik' => '7502011001850001', 'name' => 'Ahmad Mohamad', 'phone' => '081233445566', 'address' => 'Desa ' . $vGorontalo[0]->name, 'regency_id' => $regGorontalo->id, 'district_id' => $vGorontalo[0]->district_id, 'village_id' => $vGorontalo[0]->id],
            ['nik' => '7502011504780002', 'name' => 'Siti Rahmawati', 'phone' => '081399887766', 'address' => 'Desa ' . ($vGorontalo[1]->name ?? $vGorontalo[0]->name), 'regency_id' => $regGorontalo->id, 'district_id' => ($vGorontalo[1]->district_id ?? $vGorontalo[0]->district_id), 'village_id' => ($vGorontalo[1]->id ?? $vGorontalo[0]->id)],
            ['nik' => '7502022006900003', 'name' => 'Ismail Polapa', 'phone' => '082144556677', 'address' => 'Desa ' . ($vGorontalo[2]->name ?? $vGorontalo[0]->name), 'regency_id' => $regGorontalo->id, 'district_id' => ($vGorontalo[2]->district_id ?? $vGorontalo[0]->district_id), 'village_id' => ($vGorontalo[2]->id ?? $vGorontalo[0]->id)],
            ['nik' => '7502022508820004', 'name' => 'Yusuf Gobel', 'phone' => '085711223344', 'address' => 'Desa ' . ($vGorontalo[3]->name ?? $vGorontalo[0]->name), 'regency_id' => $regGorontalo->id, 'district_id' => ($vGorontalo[3]->district_id ?? $vGorontalo[0]->district_id), 'village_id' => ($vGorontalo[3]->id ?? $vGorontalo[0]->id)],
            ['nik' => '7571011003750005', 'name' => 'H. Rusli Habibie', 'phone' => '087812341234', 'address' => 'Kawasan Peternakan ' . $vKota->name, 'regency_id' => $regKotaGorontalo->id, 'district_id' => $vKota->district_id, 'village_id' => $vKota->id],
        ];

        $farmerModels = [];
        foreach ($farmers as $f) {
            $farmerModels[] = Farmer::create($f);
        }

        // 5. Buat Data Sapi Betina & Rekam Kehamilan
        $this->command->info('5. Membuat data sapi & siklus kehamilan...');
        $samples = [
            [
                'name' => 'Si Murni', 'breed' => 'Limosin', 'farmer_id' => $farmerModels[0]->id, 'village' => $vGorontalo[0],
                'ib_days_ago' => 20, 'status' => 'Pasca IB', 'cow_status' => 'Pasca IB', 'semen' => 'LIM-8890', 'officer' => $opA->name, 'op' => $opA->id
            ],
            [
                'name' => 'Si Bella', 'breed' => 'Simental', 'farmer_id' => $farmerModels[0]->id, 'village' => $vGorontalo[0],
                'ib_days_ago' => 35, 'status' => 'Estimasi 30 Hari', 'cow_status' => 'Pasca IB', 'semen' => 'SIM-4421', 'officer' => $opA->name, 'op' => $opA->id
            ],
            [
                'name' => 'Si Mega', 'breed' => 'Frisian Holstein', 'farmer_id' => $farmerModels[4]->id, 'village' => $vKota,
                'ib_days_ago' => 250, 'status' => 'Siap Melahirkan (245 Hari)', 'cow_status' => 'Siap Melahirkan', 'semen' => 'FH-7731', 'officer' => $opA->name, 'op' => $opA->id
            ],
            [
                'name' => 'Si Mawar', 'breed' => 'Limosin Cross', 'farmer_id' => $farmerModels[3]->id, 'village' => ($vGorontalo[3] ?? $vGorontalo[0]),
                'ib_days_ago' => 310, 'status' => 'Melahirkan (Selesai)', 'cow_status' => 'Pasca Melahirkan', 'semen' => 'LIM-3320', 'officer' => $opA->name, 'op' => $opA->id,
                'birth' => true, 'calf_gender' => 'Jantan'
            ],
            [
                'name' => 'Si Cantik', 'breed' => 'PO (Peranakan Ongole)', 'farmer_id' => $farmerModels[1]->id, 'village' => ($vGorontalo[1] ?? $vGorontalo[0]),
                'ib_days_ago' => 120, 'status' => 'Bunting (PKB 90 Hari)', 'cow_status' => 'Bunting', 'semen' => 'PO-1102', 'officer' => $opB->name, 'op' => $opB->id
            ],
            [
                'name' => 'Si Rosi', 'breed' => 'Brahman', 'farmer_id' => $farmerModels[2]->id, 'village' => ($vGorontalo[2] ?? $vGorontalo[0]),
                'ib_days_ago' => 284, 'status' => 'Fase Melahirkan', 'cow_status' => 'Mendekati Melahirkan', 'semen' => 'BRM-9912', 'officer' => $opB->name, 'op' => $opB->id
            ],
            [
                'name' => 'Si Melati', 'breed' => 'Bali', 'farmer_id' => $farmerModels[1]->id, 'village' => ($vGorontalo[1] ?? $vGorontalo[0]),
                'ib_days_ago' => 320, 'status' => 'Melahirkan (Selesai)', 'cow_status' => 'Pasca Melahirkan', 'semen' => 'BAL-5501', 'officer' => $opB->name, 'op' => $opB->id,
                'birth' => true, 'calf_gender' => 'Betina'
            ],
        ];

        foreach ($samples as $data) {
            $village = $data['village'];
            $district = $village->district;
            $regency = $district->regency;

            $cowNik = CattleNikGenerator::generate($village->id);

            $cow = Cow::create([
                'cow_nik' => $cowNik,
                'name' => $data['name'],
                'breed' => $data['breed'],
                'birth_date' => Carbon::now()->subYears(3)->subMonths(rand(1, 10))->format('Y-m-d'),
                'farmer_id' => $data['farmer_id'],
                'regency_id' => $regency->id,
                'district_id' => $district->id,
                'village_id' => $village->id,
                'current_status' => $data['cow_status'],
            ]);

            $ibDate = Carbon::now()->subDays($data['ib_days_ago']);

            $pregnancy = PregnancyRecord::create([
                'cow_id' => $cow->id,
                'operator_id' => $data['op'],
                'insemination_date' => $ibDate->format('Y-m-d'),
                'semen_code' => $data['semen'],
                'officer_name' => $data['officer'],
                'day30_date' => (clone $ibDate)->addDays(30)->format('Y-m-d'),
                'day90_date' => (clone $ibDate)->addDays(90)->format('Y-m-d'),
                'day245_date' => (clone $ibDate)->addDays(245)->format('Y-m-d'),
                'estimated_birth_date' => (clone $ibDate)->addDays(284)->format('Y-m-d'),
                'birth_window_end_date' => (clone $ibDate)->addDays(333)->format('Y-m-d'),
                'status' => $data['status'],
                'notes' => 'Seeding data simulasi kehamilan sapi ' . $data['name'] . ' di Gorontalo',
            ]);

            if (isset($data['birth']) && $data['birth'] === true) {
                $calfNik = 'PEDET-' . str_replace(['SPI-', '.'], '', $cowNik);
                Calf::create([
                    'pregnancy_record_id' => $pregnancy->id,
                    'cow_id' => $cow->id,
                    'calf_nik' => $calfNik,
                    'name' => 'Pedet ' . $data['name'],
                    'breed' => $data['breed'],
                    'gender' => $data['calf_gender'],
                    'birth_date' => (clone $ibDate)->addDays(284)->format('Y-m-d'),
                    'birth_weight' => rand(25, 35),
                    'condition' => 'Sehat',
                    'notes' => 'Anak sapi sehat hasil IB ' . $data['semen'],
                ]);
            }
        }

        // 6. Jalankan ReadyToGiveBirthSeeder satu kali
        $this->call(ReadyToGiveBirthSeeder::class);

        // 7. Jalankan BoalemoInseminationSeeder
        $this->call(BoalemoInseminationSeeder::class);

        $this->command->info('Seeding database Gorontalo selesai dengan sukses!');
    }
}
