<?php

namespace Tests\Unit\Models;

use App\Models\ProductionTime;
use App\Models\ProductionOrder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductionTimeTest extends TestCase
{
    use RefreshDatabase;

    public function test_puede_crear_tiempo_produccion(): void
    {
        $time = ProductionTime::factory()->create();

        $this->assertDatabaseHas('production_times', [
            'id' => $time->id,
        ]);
    }

    public function test_tiempo_pertenece_a_orden(): void
    {
        $order = ProductionOrder::factory()->create();
        $time = ProductionTime::factory()->create(['production_order_id' => $order->id]);

        $this->assertInstanceOf(ProductionOrder::class, $time->productionOrder);
        $this->assertEquals($order->id, $time->productionOrder->id);
    }

    public function test_no_tiene_timestamps(): void
    {
        $time = new ProductionTime();

        $this->assertFalse($time->timestamps);
    }

    public function test_tiempo_en_curso(): void
    {
        $time = ProductionTime::factory()->enCurso()->create();

        $this->assertNotNull($time->inicio);
        $this->assertNull($time->fin);
    }

    public function test_casts_datetime(): void
    {
        $time = ProductionTime::factory()->create();

        $this->assertInstanceOf(\Carbon\Carbon::class, $time->inicio);
        $this->assertInstanceOf(\Carbon\Carbon::class, $time->fin);
    }
}
