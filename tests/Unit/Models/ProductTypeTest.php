<?php

namespace Tests\Unit\Models;

use App\Models\ProductType;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductTypeTest extends TestCase
{
    use RefreshDatabase;

    public function test_puede_crear_tipo_producto(): void
    {
        $type = ProductType::factory()->create();

        $this->assertDatabaseHas('product_types', [
            'id' => $type->id,
            'nombre' => $type->nombre,
        ]);
    }

    public function test_tipo_tiene_muchos_productos(): void
    {
        $type = ProductType::factory()->create();
        Product::factory()->count(3)->create(['product_type_id' => $type->id]);

        $this->assertCount(3, $type->products);
        $this->assertInstanceOf(Product::class, $type->products->first());
    }

    public function test_tipo_pertenece_a_creador(): void
    {
        $user = User::factory()->create();
        $type = ProductType::factory()->create(['created_by' => $user->id]);

        $this->assertInstanceOf(User::class, $type->creator);
        $this->assertEquals($user->id, $type->creator->id);
    }
}
