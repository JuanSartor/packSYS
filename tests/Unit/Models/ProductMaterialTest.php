<?php

namespace Tests\Unit\Models;

use App\Models\ProductMaterial;
use App\Models\Product;
use App\Models\PaperCoil;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductMaterialTest extends TestCase
{
    use RefreshDatabase;

    public function test_puede_crear_material(): void
    {
        $material = ProductMaterial::factory()->create();

        $this->assertDatabaseHas('product_materials', [
            'id' => $material->id,
        ]);
    }

    public function test_material_pertenece_a_producto(): void
    {
        $product = Product::factory()->create();
        $material = ProductMaterial::factory()->create(['product_id' => $product->id]);

        $this->assertInstanceOf(Product::class, $material->product);
        $this->assertEquals($product->id, $material->product->id);
    }

    public function test_material_pertenece_a_bobina(): void
    {
        $coil = PaperCoil::factory()->create();
        $material = ProductMaterial::factory()->create(['paper_coil_id' => $coil->id]);

        $this->assertInstanceOf(PaperCoil::class, $material->paperCoil);
        $this->assertEquals($coil->id, $material->paperCoil->id);
    }

    public function test_no_tiene_timestamps(): void
    {
        $material = new ProductMaterial();

        $this->assertFalse($material->timestamps);
    }

    public function test_cast_consumo_decimal(): void
    {
        $material = ProductMaterial::factory()->create(['consumo_por_unidad' => 0.1234]);

        $this->assertEquals('0.1234', $material->consumo_por_unidad);
    }
}
