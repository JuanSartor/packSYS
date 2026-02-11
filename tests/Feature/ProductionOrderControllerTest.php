<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Product;
use App\Models\ProductionOrder;
use App\Models\OrderStatus;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductionOrderControllerTest extends TestCase
{
    use RefreshDatabase;

    private User $gestor;
    private User $operario;
    private User $vendedor;

    protected function setUp(): void
    {
        parent::setUp();

        $this->gestor = User::factory()->create(['role' => 'gestor']);
        $this->operario = User::factory()->create(['role' => 'operario']);
        $this->vendedor = User::factory()->create(['role' => 'vendedor']);
    }

    public function test_usuario_no_autenticado_no_puede_ver_ordenes(): void
    {
        $response = $this->get(route('production-orders.index'));

        $response->assertRedirect(route('login'));
    }

    public function test_gestor_puede_ver_lista_ordenes(): void
    {
        ProductionOrder::factory()->count(3)->create();

        $response = $this->actingAs($this->gestor)->get(route('production-orders.index'));

        $response->assertStatus(200);
        $response->assertViewIs('production-orders.index');
    }

    public function test_operario_puede_ver_lista_ordenes(): void
    {
        ProductionOrder::factory()->count(3)->create();

        $response = $this->actingAs($this->operario)->get(route('production-orders.index'));

        $response->assertStatus(200);
    }

    public function test_vendedor_no_puede_ver_ordenes(): void
    {
        $response = $this->actingAs($this->vendedor)->get(route('production-orders.index'));

        $response->assertStatus(403);
    }

    public function test_gestor_puede_crear_orden(): void
    {
        $product = Product::factory()->create();
        $status = OrderStatus::factory()->create();

        $data = [
            'product_id' => $product->id,
            'cantidad' => 100,
            'order_status_id' => $status->id,
        ];

        $response = $this->actingAs($this->gestor)->post(route('production-orders.store'), $data);

        $response->assertRedirect(route('production-orders.index'));

        $this->assertDatabaseHas('production_orders', [
            'product_id' => $product->id,
            'cantidad' => 100,
            'created_by' => $this->gestor->id,
        ]);
    }

    public function test_operario_puede_iniciar_orden(): void
    {
        $order = ProductionOrder::factory()->create([
            'estado' => 'pendiente',
            'started_at' => null,
        ]);

        $response = $this->actingAs($this->operario)->post(route('production-orders.start', $order));

        $response->assertRedirect();
        $this->assertNotNull($order->fresh()->started_at);
    }

    public function test_operario_puede_pausar_orden(): void
    {
        $order = ProductionOrder::factory()->enProceso()->create();

        $response = $this->actingAs($this->operario)->post(route('production-orders.pause', $order));

        $response->assertRedirect();
    }

    public function test_operario_puede_finalizar_orden(): void
    {
        $order = ProductionOrder::factory()->enProceso()->create();

        $response = $this->actingAs($this->operario)->post(route('production-orders.finish', $order));

        $response->assertRedirect();
        $this->assertNotNull($order->fresh()->finished_at);
    }

    public function test_puede_ver_detalle_orden(): void
    {
        $order = ProductionOrder::factory()->create();

        $response = $this->actingAs($this->operario)->get(route('production-orders.show', $order));

        $response->assertStatus(200);
        $response->assertViewIs('production-orders.show');
    }

    public function test_puede_eliminar_orden(): void
    {
        $order = ProductionOrder::factory()->create(['eliminado' => false]);

        $response = $this->actingAs($this->gestor)->delete(route('production-orders.destroy', $order));

        $response->assertRedirect(route('production-orders.index'));
        $this->assertTrue($order->fresh()->eliminado == 1);
    }

    public function test_validacion_producto_requerido(): void
    {
        $status = OrderStatus::factory()->create();

        $data = [
            'cantidad' => 100,
            'order_status_id' => $status->id,
        ];

        $response = $this->actingAs($this->gestor)->post(route('production-orders.store'), $data);

        $response->assertSessionHasErrors('product_id');
    }

    public function test_validacion_cantidad_requerida(): void
    {
        $product = Product::factory()->create();
        $status = OrderStatus::factory()->create();

        $data = [
            'product_id' => $product->id,
            'order_status_id' => $status->id,
        ];

        $response = $this->actingAs($this->gestor)->post(route('production-orders.store'), $data);

        $response->assertSessionHasErrors('cantidad');
    }
}
