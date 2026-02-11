<?php

namespace Tests\Unit\Models;

use App\Models\Proveedor;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProveedorTest extends TestCase
{
    use RefreshDatabase;

    public function test_puede_crear_proveedor(): void
    {
        $proveedor = Proveedor::factory()->create();

        $this->assertDatabaseHas('proveedores', [
            'id' => $proveedor->id,
            'nombre' => $proveedor->nombre,
        ]);
    }

    public function test_proveedor_tiene_muchos_productos(): void
    {
        $proveedor = Proveedor::factory()->create();
        Product::factory()->count(3)->create(['proveedor_id' => $proveedor->id]);

        $this->assertCount(3, $proveedor->products);
        $this->assertInstanceOf(Product::class, $proveedor->products->first());
    }

    public function test_proveedor_pertenece_a_creador(): void
    {
        $user = User::factory()->create();
        $proveedor = Proveedor::factory()->create(['created_by' => $user->id]);

        $this->assertInstanceOf(User::class, $proveedor->creator);
        $this->assertEquals($user->id, $proveedor->creator->id);
    }

    public function test_usa_tabla_proveedores(): void
    {
        $proveedor = new Proveedor();

        $this->assertEquals('proveedores', $proveedor->getTable());
    }
}
