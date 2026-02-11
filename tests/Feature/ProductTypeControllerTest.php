<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\ProductType;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductTypeControllerTest extends TestCase
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

    public function test_gestor_puede_ver_lista_tipos(): void
    {
        ProductType::factory()->count(3)->create();

        $response = $this->actingAs($this->gestor)->get(route('product-types.index'));

        $response->assertStatus(200);
        $response->assertViewIs('product-types.index');
    }

    public function test_vendedor_no_puede_ver_tipos(): void
    {
        $response = $this->actingAs($this->vendedor)->get(route('product-types.index'));

        $response->assertStatus(403);
    }

    public function test_gestor_puede_crear_tipo(): void
    {
        $data = [
            'nombre' => 'Tipo Test',
            'descripcion' => 'Descripción del tipo',
        ];

        $response = $this->actingAs($this->gestor)->post(route('product-types.store'), $data);

        $response->assertRedirect(route('product-types.index'));

        $this->assertDatabaseHas('product_types', [
            'nombre' => 'Tipo Test',
            'created_by' => $this->gestor->id,
        ]);
    }

    public function test_gestor_puede_ver_detalle_tipo(): void
    {
        $type = ProductType::factory()->create();

        $response = $this->actingAs($this->gestor)->get(route('product-types.show', $type));

        $response->assertStatus(200);
        $response->assertViewIs('product-types.show');
    }

    public function test_gestor_puede_actualizar_tipo(): void
    {
        $type = ProductType::factory()->create();

        $data = [
            'nombre' => 'Tipo Actualizado',
            'descripcion' => 'Nueva descripción',
        ];

        $response = $this->actingAs($this->gestor)->put(route('product-types.update', $type), $data);

        $response->assertRedirect(route('product-types.index'));

        $this->assertDatabaseHas('product_types', [
            'id' => $type->id,
            'nombre' => 'Tipo Actualizado',
        ]);
    }

    public function test_gestor_puede_eliminar_tipo(): void
    {
        $type = ProductType::factory()->create(['eliminado' => false]);

        $response = $this->actingAs($this->gestor)->delete(route('product-types.destroy', $type));

        $response->assertRedirect(route('product-types.index'));
        $this->assertTrue($type->fresh()->eliminado == 1);
    }

    public function test_validacion_nombre_requerido(): void
    {
        $data = [
            'descripcion' => 'Solo descripción',
        ];

        $response = $this->actingAs($this->gestor)->post(route('product-types.store'), $data);

        $response->assertSessionHasErrors('nombre');
    }
}
