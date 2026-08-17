<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
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
 * SiTernak-OK (Sistem Pemantauan Kehamilan Sapi Dinas Peternakan)
 * Mengisi data awal Wilayah, Admin, Operator, Master Straw Pejantan, Peternak, Sapi Betina, Rekam IB, dan Anak Sapi.
 */
class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Buat Data Wilayah (Kabupaten/Kota -> Kecamatan -> Desa)
        $reg1 = Regency::create(['code' => '35.07', 'name' => 'Kabupaten Malang']);
        $reg2 = Regency::create(['code' => '35.79', 'name' => 'Kota Batu']);

        $dist1 = District::create(['regency_id' => $reg1->id, 'code' => '35.07.12', 'name' => 'Kepanjen']);
        $dist2 = District::create(['regency_id' => $reg1->id, 'code' => '35.07.15', 'name' => 'Singosari']);
        $dist3 = District::create(['regency_id' => $reg2->id, 'code' => '35.79.01', 'name' => 'Batu']);

        $v1 = Village::create(['district_id' => $dist1->id, 'code' => '2001', 'name' => 'Panggungrejo']);
        $v2 = Village::create(['district_id' => $dist1->id, 'code' => '2002', 'name' => 'Ardirejo']);
        $v3 = Village::create(['district_id' => $dist2->id, 'code' => '2005', 'name' => 'Candirejo']);
        $v4 = Village::create(['district_id' => $dist2->id, 'code' => '2006', 'name' => 'Banjararum']);
        $v5 = Village::create(['district_id' => $dist3->id, 'code' => '2010', 'name' => 'Pesanggrahan']);

        // 2. Buat User Hak Akses (Admin Provinsi, Admin Kab. Malang, Admin Kota Batu, Operator A, Operator B)
        $adminProvinsi = User::create([
            'name' => 'Admin Provinsi Jawa Timur',
            'email' => 'admin@siternak.go.id',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'regency_id' => null, // Access ALL Regencies
            'phone' => '081234567890',
        ]);

        $adminKabMalang = User::create([
            'name' => 'Admin Kabupaten Malang',
            'email' => 'admin.malang@siternak.go.id',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'regency_id' => $reg1->id, // Scope Kab. Malang ONLY
            'phone' => '081299887766',
        ]);

        $adminKotaBatu = User::create([
            'name' => 'Admin Kota Batu',
            'email' => 'admin.batu@siternak.go.id',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'regency_id' => $reg2->id, // Scope Kota Batu ONLY
            'phone' => '081377665544',
        ]);

        $opA = User::create([
            'name' => 'Operator A (Budi Inseminator)',
            'email' => 'operator@siternak.go.id',
            'password' => Hash::make('password'),
            'role' => 'operator',
            'regency_id' => $reg1->id,
            'phone' => '089876543210',
        ]);

        $opB = User::create([
            'name' => 'Operator B (M. Taufik Inseminator)',
            'email' => 'operator2@siternak.go.id',
            'password' => Hash::make('password'),
            'role' => 'operator',
            'regency_id' => $reg1->id,
            'phone' => '087711223344',
        ]);

        // 3. Buat Database Master Kode Straw Pejantan IB
        $straws = [
            ['code' => 'LIM-8890', 'bull_name' => 'Bima Limosin Super', 'breed' => 'Limosin', 'producer' => 'BBIB Singosari'],
            ['code' => 'SIM-4421', 'bull_name' => 'Gatotkaca Simental', 'breed' => 'Simental', 'producer' => 'BBIB Singosari'],
            ['code' => 'FH-7731', 'bull_name' => 'Subali FH Perah', 'breed' => 'Frisian Holstein', 'producer' => 'BIB Lembang'],
            ['code' => 'PO-1102', 'bull_name' => 'Anoman PO Ongole', 'breed' => 'PO (Peranakan Ongole)', 'producer' => 'BBIB Singosari'],
            ['code' => 'BRM-9912', 'bull_name' => 'Perkasa Brahman', 'breed' => 'Brahman', 'producer' => 'BBIB Singosari'],
            ['code' => 'LIM-3320', 'bull_name' => 'Super Limosin II', 'breed' => 'Limosin', 'producer' => 'BBIB Singosari'],
            ['code' => 'SIM-9001', 'bull_name' => 'Megantara Simental', 'breed' => 'Simental', 'producer' => 'BBIB Singosari'],
            ['code' => 'MAD-5501', 'bull_name' => 'Sakera Madura', 'breed' => 'Madura', 'producer' => 'BIB Daerah'],
        ];

        foreach ($straws as $st) {
            BullStraw::create(array_merge($st, ['is_active' => true]));
        }

        // 4. Buat Data Peternak
        $farmers = [
            ['nik' => '3507121001850001', 'name' => 'Ahmad Sugiono', 'phone' => '081233445566', 'address' => 'Jl. Merdeka No. 12', 'regency_id' => $reg1->id, 'district_id' => $dist1->id, 'village_id' => $v1->id],
            ['nik' => '3507121504780002', 'name' => 'Siti Maryam', 'phone' => '081399887766', 'address' => 'Desa Ardirejo RT 02 RW 01', 'regency_id' => $reg1->id, 'district_id' => $dist1->id, 'village_id' => $v2->id],
            ['nik' => '3507152006900003', 'name' => 'Slamet Widodo', 'phone' => '082144556677', 'address' => 'Dusun Candirejo No. 45', 'regency_id' => $reg1->id, 'district_id' => $dist2->id, 'village_id' => $v3->id],
            ['nik' => '3507152508820004', 'name' => 'Bambang Setyo', 'phone' => '085711223344', 'address' => 'Jl. Garuda No. 8', 'regency_id' => $reg1->id, 'district_id' => $dist2->id, 'village_id' => $v4->id],
            ['nik' => '3579011003750005', 'name' => 'H. Suwandi', 'phone' => '087812341234', 'address' => 'Kawasan Peternakan Pesanggrahan', 'regency_id' => $reg2->id, 'district_id' => $dist3->id, 'village_id' => $v5->id],
        ];

        $farmerModels = [];
        foreach ($farmers as $f) {
            $farmerModels[] = Farmer::create($f);
        }

        // 5. Buat Data Sapi Betina & Rekam Kehamilan Milestone (Dipisah antara Kab. Malang & Kota Batu)
        $samples = [
            // Sapi di Kab. Malang milik Operator A (Budi Inseminator)
            [
                'name' => 'Si Murni', 'breed' => 'Limosin', 'farmer_id' => $farmerModels[0]->id, 'village' => $v1,
                'ib_days_ago' => 20, 'status' => 'Pasca IB', 'cow_status' => 'Pasca IB', 'semen' => 'LIM-8890', 'officer' => $opA->name, 'op' => $opA->id
            ],
            [
                'name' => 'Si Bella', 'breed' => 'Simental', 'farmer_id' => $farmerModels[0]->id, 'village' => $v1,
                'ib_days_ago' => 35, 'status' => 'Estimasi 30 Hari', 'cow_status' => 'Pasca IB', 'semen' => 'SIM-4421', 'officer' => $opA->name, 'op' => $opA->id
            ],

            // Sapi di Kota Batu (Suwandi)
            [
                'name' => 'Si Mega', 'breed' => 'Frisian Holstein', 'farmer_id' => $farmerModels[4]->id, 'village' => $v5,
                'ib_days_ago' => 250, 'status' => 'Siap Melahirkan (245 Hari)', 'cow_status' => 'Siap Melahirkan', 'semen' => 'FH-7731', 'officer' => $opA->name, 'op' => $opA->id
            ],

            [
                'name' => 'Si Mawar', 'breed' => 'Limosin Cross', 'farmer_id' => $farmerModels[3]->id, 'village' => $v4,
                'ib_days_ago' => 310, 'status' => 'Melahirkan (Selesai)', 'cow_status' => 'Pasca Melahirkan', 'semen' => 'LIM-3320', 'officer' => $opA->name, 'op' => $opA->id,
                'birth' => true, 'calf_gender' => 'Jantan'
            ],

            // Sapi milik Operator B (M. Taufik Inseminator)
            [
                'name' => 'Si Cantik', 'breed' => 'PO (Peranakan Ongole)', 'farmer_id' => $farmerModels[1]->id, 'village' => $v2,
                'ib_days_ago' => 120, 'status' => 'Bunting (PKB 90 Hari)', 'cow_status' => 'Bunting', 'semen' => 'PO-1102', 'officer' => $opB->name, 'op' => $opB->id
            ],
            [
                'name' => 'Si Rosi', 'breed' => 'Brahman', 'farmer_id' => $farmerModels[2]->id, 'village' => $v3,
                'ib_days_ago' => 284, 'status' => 'Fase Melahirkan', 'cow_status' => 'Mendekati Melahirkan', 'semen' => 'BRM-9912', 'officer' => $opB->name, 'op' => $opB->id
            ],
            [
                'name' => 'Si Melati', 'breed' => 'Simental Cross', 'farmer_id' => $farmerModels[1]->id, 'village' => $v2,
                'ib_days_ago' => 320, 'status' => 'Melahirkan (Selesai)', 'cow_status' => 'Pasca Melahirkan', 'semen' => 'SIM-9001', 'officer' => $opB->name, 'op' => $opB->id,
                'birth' => true, 'calf_gender' => 'Betina'
            ],
        ];

        foreach ($samples as $index => $data) {
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
                'notes' => 'Seeding data simulasi kehamilan sapi ' . $data['name'],
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
    }
}
