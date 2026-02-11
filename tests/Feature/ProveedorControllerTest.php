<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Proveedor;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProveedorControllerTest extends TestCase
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

    public function test_gestor_puede_ver_lista_proveedores(): void
    {
        Proveedor::factory()->count(3)->create();

        $response = $this->actingAs($this->gestor)->get(route('proveedores.index'));

        $response->assertStatus(200);
        $response->assertViewIs('proveedores.index');
    }

    public function test_vendedor_no_puede_ver_proveedores(): void
    {
        $response = $this->actingAs($this->vendedor)->get(route('proveedores.index'));

        $response->assertStatus(403);
    }

    public function test_gestor_puede_crear_proveedor(): void
    {
        $data = [
            'nombre' => 'Proveedor Test',
            'descripcion' => 'Descripción del proveedor',
        ];

        $response = $this->actingAs($this->gestor)->post(route('proveedores.store'), $data);

        $response->assertRedirect(route('proveedores.index'));

        $this->assertDatabaseHas('proveedores', [
            'nombre' => 'Proveedor Test',
            'created_by' => $this->gestor->id,
        ]);
    }

    public function test_gestor_puede_ver_detalle_proveedor(): void
    {
        $proveedor = Proveedor::factory()->create();

        $response = $this->actingAs($this->gestor)->get(route('proveedores.show', $proveedor));

        $response->assertStatus(200);
        $response->assertViewIs('proveedores.show');
    }

    public function test_gestor_puede_actualizar_proveedor(): void
    {
        $proveedor = Proveedor::factory()->create();

        $data = [
            'nombre' => 'Proveedor Actualizado',
            'descripcion' => 'Nueva descripción',
        ];

        $response = $this->actingAs($this->gestor)->put(route('proveedores.update', $proveedor), $data);

        $response->assertRedirect(route('proveedores.index'));

        $this->assertDatabaseHas('proveedores', [
            'id' => $proveedor->id,
            'nombre' => 'Proveedor Actualizado',
        ]);
    }

    public function test_gestor_puede_eliminar_proveedor(): void
    {
        $proveedor = Proveedor::factory()->create(['eliminado' => false]);

        $response = $this->actingAs($this->gestor)->delete(route('proveedores.destroy', $proveedor));

        $response->assertRedirect(route('proveedores.index'));
        $this->assertTrue($proveedor->fresh()->eliminado == 1);
    }

    public function test_validacion_nombre_requerido(): void
    {
        $data = [
            'descripcion' => 'Solo descripción',
        ];

        $response = $this->actingAs($this->gestor)->post(route('proveedores.store'), $data);

        $response->assertSessionHasErrors('nombre');
    }
}
