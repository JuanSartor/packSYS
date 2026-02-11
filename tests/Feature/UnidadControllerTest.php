<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Unidad;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UnidadControllerTest extends TestCase
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

    public function test_gestor_puede_ver_lista_unidades(): void
    {
        Unidad::factory()->count(3)->create();

        $response = $this->actingAs($this->gestor)->get(route('unidades.index'));

        $response->assertStatus(200);
        $response->assertViewIs('unidades.index');
    }

    public function test_vendedor_no_puede_ver_unidades(): void
    {
        $response = $this->actingAs($this->vendedor)->get(route('unidades.index'));

        $response->assertStatus(403);
    }

    public function test_gestor_puede_crear_unidad(): void
    {
        $data = [
            'descripcion' => 'Kilogramo',
        ];

        $response = $this->actingAs($this->gestor)->post(route('unidades.store'), $data);

        $response->assertRedirect(route('unidades.index'));

        $this->assertDatabaseHas('unidades', [
            'descripcion' => 'Kilogramo',
            'created_by' => $this->gestor->id,
        ]);
    }

    public function test_gestor_puede_ver_detalle_unidad(): void
    {
        $unidad = Unidad::factory()->create();

        $response = $this->actingAs($this->gestor)->get(route('unidades.show', $unidad));

        $response->assertStatus(200);
        $response->assertViewIs('unidades.show');
    }

    public function test_gestor_puede_actualizar_unidad(): void
    {
        $unidad = Unidad::factory()->create(['descripcion' => 'Original']);

        $data = [
            'descripcion' => 'Unidad Actualizada',
        ];

        $response = $this->actingAs($this->gestor)
            ->from(route('unidades.edit', $unidad))
            ->put(route('unidades.update', $unidad), $data);

        $response->assertSessionHasNoErrors();
        $response->assertRedirect(route('unidades.index'));

        $this->assertDatabaseHas('unidades', [
            'id' => $unidad->id,
            'descripcion' => 'Unidad Actualizada',
        ]);
    }

    public function test_gestor_puede_eliminar_unidad(): void
    {
        $unidad = Unidad::factory()->create(['eliminado' => false]);

        $response = $this->actingAs($this->gestor)->delete(route('unidades.destroy', $unidad));

        $response->assertRedirect(route('unidades.index'));
        $this->assertTrue($unidad->fresh()->eliminado == 1);
    }

    public function test_validacion_descripcion_requerida(): void
    {
        $data = [];

        $response = $this->actingAs($this->gestor)->post(route('unidades.store'), $data);

        $response->assertSessionHasErrors('descripcion');
    }
}
