<?php

namespace Tests\Feature;

use App\Models\Calf;
use App\Models\Cow;
use App\Models\User;
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

        $publicRoutes = ['/', '/login', '/dashboard', '/cows', '/calves', '/farmers', '/reports', '/reports/pdf', '/inseminators'];
        foreach ($publicRoutes as $uri) {
            $this->get($uri)->assertStatus(200);
        }

        $cow = Cow::first();
        $calf = Calf::first();

        if ($cow) {
            $this->get("/cows/{$cow->id}")->assertStatus(200);
        }
        if ($calf) {
            $this->get("/calves/{$calf->id}")->assertStatus(200);
        }

        $operator = User::where('role', 'operator')->first();
        if ($operator) {
            $this->actingAs($operator)->get('/dashboard')->assertStatus(200);
            $this->actingAs($operator)->get('/cows-create')->assertStatus(200);
            $this->actingAs($operator)->get('/straws')->assertStatus(200);
            $this->actingAs($operator)->get('/notifications/poll')->assertStatus(200);
            if ($cow) {
                $this->actingAs($operator)->get("/cows/{$cow->id}/edit")->assertStatus(200);
            }
            if ($calf) {
                $this->actingAs($operator)->get("/calves/{$calf->id}/edit")->assertStatus(200);
            }
        }

        $admin = User::where('role', 'admin_provinsi')->first() ?? User::where('role', 'like', 'admin%')->first();
        if ($admin) {
            $this->actingAs($admin)->get('/dashboard')->assertStatus(200);
            $this->actingAs($admin)->get('/regions')->assertStatus(200);
            $this->actingAs($admin)->get('/users')->assertStatus(200);
            $this->actingAs($admin)->get('/straws')->assertStatus(200);
        }
    }
}
