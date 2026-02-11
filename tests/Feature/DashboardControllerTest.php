<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_usuario_no_autenticado_no_puede_ver_dashboard(): void
    {
        $response = $this->get(route('dashboard'));

        $response->assertRedirect(route('login'));
    }

    public function test_usuario_autenticado_puede_ver_dashboard(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('dashboard'));

        $response->assertStatus(200);
        $response->assertViewIs('dashboard');
    }

    public function test_gestor_puede_ver_dashboard(): void
    {
        $user = User::factory()->create(['role' => 'gestor']);

        $response = $this->actingAs($user)->get(route('dashboard'));

        $response->assertStatus(200);
    }

    public function test_vendedor_puede_ver_dashboard(): void
    {
        $user = User::factory()->create(['role' => 'vendedor']);

        $response = $this->actingAs($user)->get(route('dashboard'));

        $response->assertStatus(200);
    }

    public function test_operario_puede_ver_dashboard(): void
    {
        $user = User::factory()->create(['role' => 'operario']);

        $response = $this->actingAs($user)->get(route('dashboard'));

        $response->assertStatus(200);
    }
}
