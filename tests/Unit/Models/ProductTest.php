<?php

namespace Tests\Unit\Models;

use App\Models\Product;
use App\Models\ProductType;
use App\Models\Proveedor;
use App\Models\Unidad;
use App\Models\User;
use App\Models\ProductPrice;
use App\Models\ProductionOrder;
use App\Models\StockMovement;
use App\Models\SaleItem;
use App\Models\PaperCoil;
use App\Models\ProductMaterial;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductTest extends TestCase
{
    use RefreshDatabase;

    public function test_puede_crear_producto(): void
    {
        $product = Product::factory()->create();

        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'name' => $product->name,
        ]);
    }

    public function test_producto_pertenece_a_tipo_producto(): void
    {
        $productType = ProductType::factory()->create();
        $product = Product::factory()->create(['product_type_id' => $productType->id]);

        $this->assertInstanceOf(ProductType::class, $product->productType);
        $this->assertEquals($productType->id, $product->productType->id);
    }

    public function test_producto_pertenece_a_proveedor(): void
    {
        $proveedor = Proveedor::factory()->create();
        $product = Product::factory()->create(['proveedor_id' => $proveedor->id]);

        $this->assertInstanceOf(Proveedor::class, $product->proveedor);
        $this->assertEquals($proveedor->id, $product->proveedor->id);
    }

    public function test_producto_pertenece_a_unidad(): void
    {
        $unidad = Unidad::factory()->create();
        $product = Product::factory()->create(['unidad_id' => $unidad->id]);

        $this->assertInstanceOf(Unidad::class, $product->unidad);
        $this->assertEquals($unidad->id, $product->unidad->id);
    }

    public function test_producto_pertenece_a_creador(): void
    {
        $user = User::factory()->create();
        $product = Product::factory()->create(['created_by' => $user->id]);

        $this->assertInstanceOf(User::class, $product->creator);
        $this->assertEquals($user->id, $product->creator->id);
    }

    public function test_producto_tiene_muchos_precios(): void
    {
        $product = Product::factory()->create();
        ProductPrice::factory()->count(3)->create(['product_id' => $product->id]);

        $this->assertCount(3, $product->prices);
        $this->assertInstanceOf(ProductPrice::class, $product->prices->first());
    }

    public function test_producto_obtiene_precio_actual(): void
    {
        $product = Product::factory()->create();

        ProductPrice::factory()->create([
            'product_id' => $product->id,
            'vigente_desde' => now()->subDays(10),
            'precio_venta' => 100,
        ]);

        $precioActual = ProductPrice::factory()->create([
            'product_id' => $product->id,
            'vigente_desde' => now()->subDay(),
            'precio_venta' => 150,
        ]);

        $this->assertEquals($precioActual->id, $product->currentPrice()->id);
    }

    public function test_producto_tiene_muchas_ordenes_produccion(): void
    {
        $product = Product::factory()->create();
        ProductionOrder::factory()->count(2)->create(['product_id' => $product->id]);

        $this->assertCount(2, $product->productionOrders);
        $this->assertInstanceOf(ProductionOrder::class, $product->productionOrders->first());
    }

    public function test_producto_tiene_muchos_movimientos_stock(): void
    {
        $product = Product::factory()->create();
        StockMovement::factory()->count(3)->create(['product_id' => $product->id]);

        $this->assertCount(3, $product->stockMovements);
        $this->assertInstanceOf(StockMovement::class, $product->stockMovements->first());
    }

    public function test_producto_tiene_muchos_items_venta(): void
    {
        $product = Product::factory()->create();
        SaleItem::factory()->count(2)->create(['product_id' => $product->id]);

        $this->assertCount(2, $product->saleItems);
        $this->assertInstanceOf(SaleItem::class, $product->saleItems->first());
    }

    public function test_producto_tiene_muchos_materiales(): void
    {
        $product = Product::factory()->create();
        ProductMaterial::factory()->count(2)->create(['product_id' => $product->id]);

        $this->assertCount(2, $product->materials);
        $this->assertInstanceOf(ProductMaterial::class, $product->materials->first());
    }

    public function test_producto_pertenece_a_muchas_bobinas(): void
    {
        $product = Product::factory()->create();
        $coil1 = PaperCoil::factory()->create();
        $coil2 = PaperCoil::factory()->create();

        ProductMaterial::factory()->create([
            'product_id' => $product->id,
            'paper_coil_id' => $coil1->id,
            'consumo_por_unidad' => 0.5,
        ]);
        ProductMaterial::factory()->create([
            'product_id' => $product->id,
            'paper_coil_id' => $coil2->id,
            'consumo_por_unidad' => 0.3,
        ]);

        $this->assertCount(2, $product->paperCoils);
        $this->assertEquals(0.5, $product->paperCoils->first()->pivot->consumo_por_unidad);
    }

    public function test_producto_con_bobina_tiene_medidas(): void
    {
        $product = Product::factory()->conBobina()->create();

        $this->assertTrue($product->usa_bobina);
        $this->assertNotNull($product->ancho);
        $this->assertNotNull($product->largo);
    }

    public function test_producto_sin_bobina_no_tiene_medidas(): void
    {
        $product = Product::factory()->sinBobina()->create();

        $this->assertFalse($product->usa_bobina);
        $this->assertNull($product->ancho);
        $this->assertNull($product->largo);
    }

    public function test_casts_correctos(): void
    {
        $product = Product::factory()->create([
            'stock_actual' => 100.50,
            'stock_minimo' => 10.25,
            'usa_bobina' => true,
        ]);

        $this->assertIsFloat($product->stock_actual + 0);
        $this->assertIsFloat($product->stock_minimo + 0);
        $this->assertIsBool($product->usa_bobina);
    }
}
