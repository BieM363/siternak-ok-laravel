<?php

namespace Tests\Feature;

use App\Models\BullStraw;
use App\Models\Calf;
use App\Models\Cow;
use App\Models\Farmer;
use App\Models\PregnancyRecord;
use App\Models\User;
use App\Models\Village;
use Carbon\Carbon;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    use RefreshDatabase;

    public function test_the_application_returns_a_successful_response(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }

    public function test_all_updated_blade_views_render_successfully_across_roles(): void
    {
        $this->seed(DatabaseSeeder::class);

        $publicRoutes = [
            '/', '/login', '/dashboard', '/confirmations', '/cows', '/pregnancies',
            '/calves', '/farmers', '/reports', '/reports/pdf', '/inseminators',
        ];
        foreach ($publicRoutes as $uri) {
            $this->get($uri)->assertStatus(200);
        }

        // Verifikasi ekspor Excel (.xlsx) valid untuk Rekap & Data Kebuntingan
        $this->get('/reports/excel')
            ->assertStatus(200)
            ->assertHeader('content-type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');

        $this->get('/pregnancies/excel')
            ->assertStatus(200)
            ->assertHeader('content-type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');

        $this->get('/calves/excel')
            ->assertStatus(200)
            ->assertHeader('content-type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');

        $cow = Cow::first();
        $calf = Calf::first();

        if ($cow) {
            $this->get("/cows/{$cow->id}")->assertStatus(200);
        }
        if ($calf) {
            $this->get("/calves/{$calf->id}")->assertStatus(200);
        }

        // Role 3: Inseminator (Mobile-First Dashboard + Input IB lintas kabupaten)
        $operator = User::where('role', 'operator')->first();
        $this->assertNotNull($operator);
        $this->actingAs($operator)->get('/dashboard')->assertStatus(200);
        $this->actingAs($operator)->get('/cows-create')->assertStatus(200);
        $this->actingAs($operator)->get('/pregnancies')->assertStatus(200);
        $this->actingAs($operator)->get('/straws')->assertStatus(200);
        $this->actingAs($operator)->get('/notifications/poll')->assertStatus(200);

        // Role 2: Admin Kabupaten/Kota (Recorder Dinas — dapat input IB susulan / backdate)
        $adminKab = User::where('role', 'admin')->whereNotNull('regency_id')->first();
        $this->assertNotNull($adminKab);
        $this->actingAs($adminKab)->get('/dashboard')->assertStatus(200);
        $this->actingAs($adminKab)->get('/cows-create')->assertStatus(200);
        $this->actingAs($adminKab)->get('/pregnancies')->assertStatus(200);
        $this->actingAs($adminKab)->get('/farmers?nik_status=without_nik')->assertStatus(200);

        // Role 1: Admin Provinsi
        $adminProv = User::where('role', 'admin')->whereNull('regency_id')->first();
        $this->assertNotNull($adminProv);
        $this->actingAs($adminProv)->get('/dashboard')->assertStatus(200);
        $this->actingAs($adminProv)->get('/regions')->assertStatus(200);
        $this->actingAs($adminProv)->get('/users')->assertStatus(200);
        $this->actingAs($adminProv)->get('/straws')->assertStatus(200);
    }

    public function test_disnakbun_revision_workflows_custom_farmer_auto_name_custom_straw_backdate_and_ib_ke_2(): void
    {
        $this->seed(DatabaseSeeder::class);

        $inseminator = User::where('role', 'operator')->firstOrFail();
        $adminKab = User::where('role', 'admin')->whereNotNull('regency_id')->firstOrFail();
        $village = Village::with('district.regency')->firstOrFail();

        // 1. Inseminator input IB dengan Peternak Custom TANPA NIK, Nama Sapi KOSONG (auto "[Nama Pemilik] 1"), Straw Custom (3 kolom), dan Tanggal Inseminasi Backdate
        $backdate = Carbon::today()->subDays(15)->format('Y-m-d');
        $response = $this->actingAs($inseminator)->post('/cows', [
            'farmer_mode' => 'custom',
            'custom_farmer_name' => 'Pak Anton Sapi',
            'custom_farmer_nik' => '',
            'custom_farmer_phone' => '081200001111',
            'village_id' => $village->id,
            'breed' => 'Limosin',
            'name' => '', // Kosong -> otomatis "Pak Anton Sapi 1"
            'insemination_date' => $backdate,
            'straw_mode' => 'custom',
            'straw_rumpun' => 'Simental',
            'straw_kode_bull' => 'SIM-OLD-99',
            'straw_kode_batch' => 'B24-09',
            'straw_producer' => 'Stok Mandiri',
            'redirect_to' => 'dashboard',
        ]);

        $response->assertRedirect('/dashboard');

        $farmer = Farmer::where('name', 'Pak Anton Sapi')->first();
        $this->assertNotNull($farmer);
        $this->assertNull($farmer->nik);

        $cow = Cow::where('farmer_id', $farmer->id)->first();
        $this->assertNotNull($cow);
        $this->assertEquals('Pak Anton Sapi 1', $cow->name);

        $ib1 = $cow->pregnancyRecords()->first();
        $this->assertNotNull($ib1);
        $this->assertEquals(1, $ib1->ib_ke);
        $this->assertEquals('Simental', $ib1->straw_rumpun);
        $this->assertEquals('SIM-OLD-99', $ib1->straw_kode_bull);
        $this->assertEquals('B24-09', $ib1->straw_kode_batch);
        $this->assertTrue($ib1->is_custom_straw);

        // 2. Tandai IB ke-1 Tidak Bunting / Gagal -> siap IB ke-2
        $this->actingAs($inseminator)->put("/pregnancy/{$ib1->id}/update-status", [
            'action' => 'confirm_no',
        ])->assertRedirect();

        $this->assertEquals('Gagal/Keguguran', $ib1->fresh()->status);
        $this->assertTrue($cow->fresh()->canReceiveNextIb());
        $this->assertEquals(2, $cow->fresh()->getNextIbNumber());

        // 3. Input IB Lanjutan (IB ke-2) langsung pada sapi yang sama tanpa input ulang peternak & sapi
        $masterStraw = BullStraw::firstOrFail();
        $this->actingAs($inseminator)->post('/pregnancy', [
            'cow_id' => $cow->id,
            'insemination_date' => Carbon::today()->subDays(95)->format('Y-m-d'),
            'straw_mode' => 'master',
            'bull_straw_id' => $masterStraw->id,
        ])->assertRedirect("/cows/{$cow->id}");

        $ib2 = $cow->pregnancyRecords()->where('ib_ke', 2)->first();
        $this->assertNotNull($ib2);
        // Karena backdate 95 hari (melewati hari 30+3 dan hari 90+3), auto-advance otomatis memajukan ke Bunting Terkonfirmasi
        $this->assertEquals('Siap Melahirkan (245 Hari)', $ib2->status);
        $this->assertEquals($masterStraw->breed, $ib2->straw_rumpun);
        $this->assertEquals($masterStraw->effective_kode_bull, $ib2->straw_kode_bull);

        // 4. Filter Tanpa NIK berfungsi pada Daftar Pemilik
        $this->actingAs($adminKab)->get('/farmers?nik_status=without_nik')->assertStatus(200);
    }
}
