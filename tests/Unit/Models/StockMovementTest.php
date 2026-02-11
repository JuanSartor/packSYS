<?php

namespace Tests\Unit\Models;

use App\Models\StockMovement;
use App\Models\Product;
use App\Models\PaperCoil;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StockMovementTest extends TestCase
{
    use RefreshDatabase;

    public function test_puede_crear_movimiento(): void
    {
        $movement = StockMovement::factory()->create();

        $this->assertDatabaseHas('stock_movements', [
            'id' => $movement->id,
        ]);
    }

    public function test_movimiento_pertenece_a_producto(): void
    {
        $product = Product::factory()->create();
        $movement = StockMovement::factory()->create(['product_id' => $product->id]);

        $this->assertInstanceOf(Product::class, $movement->product);
        $this->assertEquals($product->id, $movement->product->id);
    }

    public function test_movimiento_pertenece_a_bobina(): void
    {
        $coil = PaperCoil::factory()->create();
        $movement = StockMovement::factory()->paraBobina()->create(['paper_coil_id' => $coil->id]);

        $this->assertInstanceOf(PaperCoil::class, $movement->paperCoil);
        $this->assertEquals($coil->id, $movement->paperCoil->id);
    }

    public function test_movimiento_para_bobina(): void
    {
        $movement = StockMovement::factory()->paraBobina()->create();

        $this->assertNull($movement->product_id);
        $this->assertNotNull($movement->paper_coil_id);
    }

    public function test_cast_cantidad_decimal(): void
    {
        $movement = StockMovement::factory()->create(['cantidad' => 50.75]);

        $this->assertEquals('50.75', $movement->cantidad);
    }

    public function test_tipos_movimiento(): void
    {
        $tipos = ['entrada', 'salida', 'ajuste'];

        foreach ($tipos as $tipo) {
            $movement = StockMovement::factory()->create(['tipo' => $tipo]);
            $this->assertEquals($tipo, $movement->tipo);
        }
    }
}
