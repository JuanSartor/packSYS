<?php

namespace Tests\Unit\Models;

use App\Models\ProductPrice;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductPriceTest extends TestCase
{
    use RefreshDatabase;

    public function test_puede_crear_precio(): void
    {
        $price = ProductPrice::factory()->create();

        $this->assertDatabaseHas('product_prices', [
            'id' => $price->id,
        ]);
    }

    public function test_precio_pertenece_a_producto(): void
    {
        $product = Product::factory()->create();
        $price = ProductPrice::factory()->create(['product_id' => $product->id]);

        $this->assertInstanceOf(Product::class, $price->product);
        $this->assertEquals($product->id, $price->product->id);
    }

    public function test_precio_pertenece_a_creador(): void
    {
        $user = User::factory()->create();
        $price = ProductPrice::factory()->create(['created_by' => $user->id]);

        $this->assertInstanceOf(User::class, $price->creator);
        $this->assertEquals($user->id, $price->creator->id);
    }

    public function test_no_tiene_timestamps(): void
    {
        $price = new ProductPrice();

        $this->assertFalse($price->timestamps);
    }

    public function test_casts_correctos(): void
    {
        $price = ProductPrice::factory()->create([
            'costo' => 100.50,
            'precio_venta' => 150.75,
            'vigente_desde' => now(),
        ]);

        $this->assertIsFloat($price->costo + 0);
        $this->assertIsFloat($price->precio_venta + 0);
        $this->assertInstanceOf(\Carbon\Carbon::class, $price->vigente_desde);
    }

    public function test_precio_venta_mayor_que_costo(): void
    {
        $price = ProductPrice::factory()->create();

        $this->assertGreaterThan($price->costo, $price->precio_venta);
    }
}
