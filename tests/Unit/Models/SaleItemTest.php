<?php

namespace Tests\Unit\Models;

use App\Models\SaleItem;
use App\Models\Sale;
use App\Models\Product;
use App\Models\ProductPrice;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SaleItemTest extends TestCase
{
    use RefreshDatabase;

    public function test_puede_crear_item_venta(): void
    {
        $item = SaleItem::factory()->create();

        $this->assertDatabaseHas('sale_items', [
            'id' => $item->id,
        ]);
    }

    public function test_item_pertenece_a_venta(): void
    {
        $sale = Sale::factory()->create();
        $item = SaleItem::factory()->create(['sale_id' => $sale->id]);

        $this->assertInstanceOf(Sale::class, $item->sale);
        $this->assertEquals($sale->id, $item->sale->id);
    }

    public function test_item_pertenece_a_producto(): void
    {
        $product = Product::factory()->create();
        $item = SaleItem::factory()->create(['product_id' => $product->id]);

        $this->assertInstanceOf(Product::class, $item->product);
        $this->assertEquals($product->id, $item->product->id);
    }

    public function test_item_pertenece_a_precio_producto(): void
    {
        $price = ProductPrice::factory()->create();
        $item = SaleItem::factory()->create(['product_price_id' => $price->id]);

        $this->assertInstanceOf(ProductPrice::class, $item->productPrice);
        $this->assertEquals($price->id, $item->productPrice->id);
    }

    public function test_no_tiene_timestamps(): void
    {
        $item = new SaleItem();

        $this->assertFalse($item->timestamps);
    }

    public function test_cast_precio_decimal(): void
    {
        $item = SaleItem::factory()->create(['precio_unitario_venta' => 250.50]);

        $this->assertEquals('250.50', $item->precio_unitario_venta);
    }
}
