<?php

namespace Tests\Unit\Models;

use App\Models\ProductionOrder;
use App\Models\Product;
use App\Models\OrderStatus;
use App\Models\User;
use App\Models\ProductionTime;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductionOrderTest extends TestCase
{
    use RefreshDatabase;

    public function test_puede_crear_orden_produccion(): void
    {
        $order = ProductionOrder::factory()->create();

        $this->assertDatabaseHas('production_orders', [
            'id' => $order->id,
        ]);
    }

    public function test_orden_pertenece_a_producto(): void
    {
        $product = Product::factory()->create();
        $order = ProductionOrder::factory()->create(['product_id' => $product->id]);

        $this->assertInstanceOf(Product::class, $order->product);
        $this->assertEquals($product->id, $order->product->id);
    }

    public function test_orden_pertenece_a_estado(): void
    {
        $status = OrderStatus::factory()->create();
        $order = ProductionOrder::factory()->create(['order_status_id' => $status->id]);

        $this->assertInstanceOf(OrderStatus::class, $order->orderStatus);
        $this->assertEquals($status->id, $order->orderStatus->id);
    }

    public function test_orden_pertenece_a_creador(): void
    {
        $user = User::factory()->create();
        $order = ProductionOrder::factory()->create(['created_by' => $user->id]);

        $this->assertInstanceOf(User::class, $order->creator);
        $this->assertEquals($user->id, $order->creator->id);
    }

    public function test_orden_tiene_muchos_tiempos_produccion(): void
    {
        $order = ProductionOrder::factory()->create();
        ProductionTime::factory()->count(2)->create(['production_order_id' => $order->id]);

        $this->assertCount(2, $order->productionTimes);
        $this->assertInstanceOf(ProductionTime::class, $order->productionTimes->first());
    }

    public function test_orden_en_proceso(): void
    {
        $order = ProductionOrder::factory()->enProceso()->create();

        $this->assertEquals('produccion', $order->estado);
        $this->assertNotNull($order->started_at);
        $this->assertNull($order->finished_at);
    }

    public function test_orden_completada(): void
    {
        $order = ProductionOrder::factory()->completado()->create();

        $this->assertEquals('finalizada', $order->estado);
        $this->assertNotNull($order->started_at);
        $this->assertNotNull($order->finished_at);
    }

    public function test_casts_datetime(): void
    {
        $order = ProductionOrder::factory()->completado()->create();

        $this->assertInstanceOf(\Carbon\Carbon::class, $order->started_at);
        $this->assertInstanceOf(\Carbon\Carbon::class, $order->finished_at);
    }
}
