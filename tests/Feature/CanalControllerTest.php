<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Canal;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CanalControllerTest extends TestCase
{
    use RefreshDatabase;

    private User $gestor;
    private User $vendedor;

    protected function setUp(): void
    {
        parent::setUp();

        $this->gestor = User::factory()->create(['role' => 'gestor']);
        $this->vendedor = User::factory()->create(['role' => 'vendedor']);
    }

    public function test_gestor_puede_ver_lista_canales(): void
    {
        Canal::factory()->count(3)->create();

        $response = $this->actingAs($this->gestor)->get(route('canales.index'));

        $response->assertStatus(200);
        $response->assertViewIs('canales.index');
    }

    public function test_vendedor_no_puede_ver_canales(): void
    {
        $response = $this->actingAs($this->vendedor)->get(route('canales.index'));

        $response->assertStatus(403);
    }

    public function test_gestor_puede_crear_canal(): void
    {
        $data = [
            'descripcion' => 'Canal de Prueba',
        ];

        $response = $this->actingAs($this->gestor)->post(route('canales.store'), $data);

        $response->assertRedirect(route('canales.index'));

        $this->assertDatabaseHas('canales', [
            'descripcion' => 'Canal de Prueba',
        ]);
    }

    public function test_gestor_puede_actualizar_canal(): void
    {
        $canal = Canal::factory()->create();

        $data = [
            'descripcion' => 'Canal Actualizado',
        ];

        $response = $this->actingAs($this->gestor)->put(route('canales.update', $canal), $data);

        $response->assertRedirect(route('canales.index'));

        $this->assertDatabaseHas('canales', [
            'id' => $canal->id,
            'descripcion' => 'Canal Actualizado',
        ]);
    }

    public function test_gestor_puede_eliminar_canal(): void
    {
        $canal = Canal::factory()->create(['eliminado' => false]);

        $response = $this->actingAs($this->gestor)->delete(route('canales.destroy', $canal));

        $response->assertRedirect(route('canales.index'));
        $this->assertTrue($canal->fresh()->eliminado == 1);
    }

    public function test_validacion_descripcion_requerida(): void
    {
        $data = [];

        $response = $this->actingAs($this->gestor)->post(route('canales.store'), $data);

        $response->assertSessionHasErrors('descripcion');
    }
}
