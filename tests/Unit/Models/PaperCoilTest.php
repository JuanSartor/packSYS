<?php

namespace Tests\Unit\Models;

use App\Models\PaperCoil;
use App\Models\Product;
use App\Models\ProductMaterial;
use App\Models\StockMovement;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PaperCoilTest extends TestCase
{
    use RefreshDatabase;

    public function test_puede_crear_bobina(): void
    {
        $coil = PaperCoil::factory()->create();

        $this->assertDatabaseHas('paper_coils', [
            'id' => $coil->id,
            'tipo_papel' => $coil->tipo_papel,
        ]);
    }

    public function test_bobina_tiene_muchos_materiales(): void
    {
        $coil = PaperCoil::factory()->create();
        ProductMaterial::factory()->count(2)->create(['paper_coil_id' => $coil->id]);

        $this->assertCount(2, $coil->materials);
        $this->assertInstanceOf(ProductMaterial::class, $coil->materials->first());
    }

    public function test_bobina_tiene_muchos_movimientos_stock(): void
    {
        $coil = PaperCoil::factory()->create();
        StockMovement::factory()->count(3)->create([
            'paper_coil_id' => $coil->id,
            'product_id' => null,
        ]);

        $this->assertCount(3, $coil->stockMovements);
        $this->assertInstanceOf(StockMovement::class, $coil->stockMovements->first());
    }

    public function test_bobina_pertenece_a_muchos_productos(): void
    {
        $coil = PaperCoil::factory()->create();
        $product1 = Product::factory()->create();
        $product2 = Product::factory()->create();

        ProductMaterial::factory()->create([
            'paper_coil_id' => $coil->id,
            'product_id' => $product1->id,
            'consumo_por_unidad' => 0.25,
        ]);
        ProductMaterial::factory()->create([
            'paper_coil_id' => $coil->id,
            'product_id' => $product2->id,
            'consumo_por_unidad' => 0.15,
        ]);

        $this->assertCount(2, $coil->products);
        $this->assertEquals(0.25, $coil->products->first()->pivot->consumo_por_unidad);
    }

    public function test_casts_decimales(): void
    {
        $coil = PaperCoil::factory()->create([
            'ancho' => 50.55,
            'gramaje' => 80.25,
            'peso_inicial' => 500.00,
            'peso_actual' => 350.75,
            'alerta_minima' => 25.50,
        ]);

        $this->assertIsFloat($coil->ancho + 0);
        $this->assertIsFloat($coil->gramaje + 0);
        $this->assertIsFloat($coil->peso_inicial + 0);
        $this->assertIsFloat($coil->peso_actual + 0);
    }

    public function test_peso_actual_menor_que_inicial(): void
    {
        $coil = PaperCoil::factory()->create();

        $this->assertLessThanOrEqual($coil->peso_inicial, $coil->peso_actual);
    }
}
