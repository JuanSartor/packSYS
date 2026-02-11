<?php

namespace Tests\Unit\Models;

use App\Models\Unidad;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UnidadTest extends TestCase
{
    use RefreshDatabase;

    public function test_puede_crear_unidad(): void
    {
        $unidad = Unidad::factory()->create();

        $this->assertDatabaseHas('unidades', [
            'id' => $unidad->id,
            'descripcion' => $unidad->descripcion,
        ]);
    }

    public function test_unidad_tiene_muchos_productos(): void
    {
        $unidad = Unidad::factory()->create();
        Product::factory()->count(3)->create(['unidad_id' => $unidad->id]);

        $this->assertCount(3, $unidad->products);
        $this->assertInstanceOf(Product::class, $unidad->products->first());
    }

    public function test_unidad_pertenece_a_creador(): void
    {
        $user = User::factory()->create();
        $unidad = Unidad::factory()->create(['created_by' => $user->id]);

        $this->assertInstanceOf(User::class, $unidad->creator);
        $this->assertEquals($user->id, $unidad->creator->id);
    }

    public function test_usa_tabla_unidades(): void
    {
        $unidad = new Unidad();

        $this->assertEquals('unidades', $unidad->getTable());
    }
}
