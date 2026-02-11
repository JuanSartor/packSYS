<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\OrderStatus;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrderStatusControllerTest extends TestCase
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

    public function test_gestor_puede_ver_lista_estados(): void
    {
        OrderStatus::factory()->count(3)->create();

        $response = $this->actingAs($this->gestor)->get(route('order-statuses.index'));

        $response->assertStatus(200);
        $response->assertViewIs('order-statuses.index');
    }

    public function test_vendedor_no_puede_ver_estados(): void
    {
        $response = $this->actingAs($this->vendedor)->get(route('order-statuses.index'));

        $response->assertStatus(403);
    }

    public function test_gestor_puede_crear_estado(): void
    {
        $data = [
            'nombre' => 'En Espera',
            'descripcion' => 'Orden en espera de materiales',
        ];

        $response = $this->actingAs($this->gestor)->post(route('order-statuses.store'), $data);

        $response->assertRedirect(route('order-statuses.index'));

        $this->assertDatabaseHas('order_status', [
            'nombre' => 'En Espera',
            'created_by' => $this->gestor->id,
        ]);
    }

    public function test_gestor_puede_actualizar_estado(): void
    {
        $status = OrderStatus::factory()->create();

        $data = [
            'nombre' => 'Estado Actualizado',
            'descripcion' => 'Nueva descripción',
        ];

        $response = $this->actingAs($this->gestor)->put(route('order-statuses.update', $status), $data);

        $response->assertRedirect(route('order-statuses.index'));

        $this->assertDatabaseHas('order_status', [
            'id' => $status->id,
            'nombre' => 'Estado Actualizado',
        ]);
    }

    public function test_gestor_puede_eliminar_estado(): void
    {
        $status = OrderStatus::factory()->create(['eliminado' => false]);

        $response = $this->actingAs($this->gestor)->delete(route('order-statuses.destroy', $status));

        $response->assertRedirect(route('order-statuses.index'));
        $this->assertTrue($status->fresh()->eliminado == 1);
    }

    public function test_validacion_nombre_requerido(): void
    {
        $data = [
            'descripcion' => 'Solo descripción',
        ];

        $response = $this->actingAs($this->gestor)->post(route('order-statuses.store'), $data);

        $response->assertSessionHasErrors('nombre');
    }
}
