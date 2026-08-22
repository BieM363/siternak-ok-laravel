<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Regency;
use App\Models\District;
use App\Models\Village;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\File;

class ScrapeGorontaloRegions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'regions:scrape-gorontalo {--seed-db : Langsung masukkan data ke database}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Scraping data wilayah Provinsi Gorontalo (Kabupaten, Kota, Kecamatan, Desa/Kelurahan) dari API resmi';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('Memulai scraping data wilayah Provinsi Gorontalo (Kode Provinsi: 75)...');

        $regCodeMap = [
            '7501' => '75.01', // Kabupaten Boalemo
            '7502' => '75.02', // Kabupaten Gorontalo
            '7503' => '75.03', // Kabupaten Pohuwato
            '7504' => '75.04', // Kabupaten Bone Bolango
            '7505' => '75.05', // Kabupaten Gorontalo Utara
            '7571' => '75.71', // Kota Gorontalo
        ];

        $regenciesUrl = 'https://emsifa.github.io/api-wilayah-indonesia/api/regencies/75.json';
        $response = Http::timeout(30)->get($regenciesUrl);

        if (!$response->successful()) {
            $this->error('Gagal mengambil data Kabupaten/Kota dari API.');
            return self::FAILURE;
        }

        $regencies = $response->json();
        $result = [];
        $totalDistricts = 0;
        $totalVillages = 0;

        foreach ($regencies as $reg) {
            $regId = $reg['id'];
            $regCode = $regCodeMap[$regId] ?? (substr($regId, 0, 2) . '.' . substr($regId, 2));
            $regName = ucwords(strtolower($reg['name']));
            $regName = str_replace(['Kab. ', 'Kota '], ['Kabupaten ', 'Kota '], $regName);

            $this->line("  -> Mengambil data: {$regName} ({$regCode})...");

            $distUrl = "https://emsifa.github.io/api-wilayah-indonesia/api/districts/{$regId}.json";
            $distResponse = Http::timeout(30)->get($distUrl);
            $districts = $distResponse->successful() ? $distResponse->json() : [];

            $distData = [];
            foreach ($districts as $dist) {
                $totalDistricts++;
                $distId = $dist['id'];
                $distName = ucwords(strtolower($dist['name']));
                $distSuffix = substr($distId, 4);
                $distCode = $regCode . '.' . str_pad($distSuffix, 2, '0', STR_PAD_LEFT);

                $villUrl = "https://emsifa.github.io/api-wilayah-indonesia/api/villages/{$distId}.json";
                $villResponse = Http::timeout(30)->get($villUrl);
                $villages = $villResponse->successful() ? $villResponse->json() : [];

                $villData = [];
                foreach ($villages as $vill) {
                    $totalVillages++;
                    $villId = $vill['id'];
                    $villName = ucwords(strtolower($vill['name']));
                    $villCode = substr($villId, -4);

                    $villData[] = [
                        'raw_id' => $villId,
                        'code' => $villCode,
                        'name' => $villName,
                    ];
                }

                $distData[] = [
                    'raw_id' => $distId,
                    'code' => $distCode,
                    'name' => $distName,
                    'villages' => $villData,
                ];
            }

            $result[] = [
                'raw_id' => $regId,
                'code' => $regCode,
                'name' => $regName,
                'districts' => $distData,
            ];
        }

        $dataDir = database_path('data');
        if (!File::isDirectory($dataDir)) {
            File::makeDirectory($dataDir, 0755, true);
        }

        $filePath = $dataDir . '/gorontalo_regions.json';
        File::put($filePath, json_encode($result, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        $this->info("Dataset wilayah Gorontalo berhasil disimpan ke: {$filePath}");
        $this->info("Total: " . count($result) . " Kabupaten/Kota, {$totalDistricts} Kecamatan, {$totalVillages} Desa/Kelurahan.");

        if ($this->option('seed-db')) {
            $this->info('Menyimpan data ke database...');
            foreach ($result as $rData) {
                $regency = Regency::updateOrCreate(
                    ['code' => $rData['code']],
                    ['name' => $rData['name']]
                );

                foreach ($rData['districts'] as $dData) {
                    $district = District::updateOrCreate(
                        ['code' => $dData['code']],
                        [
                            'regency_id' => $regency->id,
                            'name' => $dData['name'],
                        ]
                    );

                    foreach ($dData['villages'] as $vData) {
                        Village::updateOrCreate(
                            [
                                'district_id' => $district->id,
                                'code' => $vData['code'],
                                'name' => $vData['name'],
                            ],
                            []
                        );
                    }
                }
            }
            $this->info('Sinkronisasi database wilayah Gorontalo selesai!');
        }

        return self::SUCCESS;
    }
}
