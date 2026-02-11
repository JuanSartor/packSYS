<?php

namespace Tests\Browser;

use App\Models\User;
use App\Models\Product;
use App\Models\ProductType;
use App\Models\Proveedor;
use App\Models\Unidad;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class ProductTest extends DuskTestCase
{
    use DatabaseMigrations;

    private User $gestor;

    protected function setUp(): void
    {
        parent::setUp();
        $this->gestor = User::factory()->create(['role' => 'gestor']);
    }

    public function test_usuario_puede_ver_lista_productos(): void
    {
        Product::factory()->count(3)->create();

        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->gestor)
                ->visit('/products')
                ->assertSee('Productos')
                ->assertPresent('table');
        });
    }

    public function test_gestor_puede_navegar_a_crear_producto(): void
    {
        ProductType::factory()->create();
        Proveedor::factory()->create();
        Unidad::factory()->create();

        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->gestor)
                ->visit('/products')
                ->clickLink('Nuevo Producto')
                ->assertPathIs('/products/create')
                ->assertSee('Crear Producto');
        });
    }

    public function test_gestor_puede_crear_producto_mediante_formulario(): void
    {
        $productType = ProductType::factory()->create(['nombre' => 'Bolsas']);
        $proveedor = Proveedor::factory()->create(['nombre' => 'Proveedor Test']);
        $unidad = Unidad::factory()->create(['descripcion' => 'Unidad']);

        $this->browse(function (Browser $browser) use ($productType, $proveedor, $unidad) {
            $browser->loginAs($this->gestor)
                ->visit('/products/create')
                ->type('name', 'Producto de Prueba')
                ->type('descripcion', 'Descripcion del producto')
                ->select('product_type_id', $productType->id)
                ->select('proveedor_id', $proveedor->id)
                ->select('unidad_id', $unidad->id)
                ->type('stock_actual', '100')
                ->type('stock_minimo', '10')
                ->type('costo', '50')
                ->type('precio_venta', '75')
                ->press('Guardar')
                ->assertPathIs('/products')
                ->assertSee('Producto creado exitosamente');
        });
    }

    public function test_gestor_puede_editar_producto(): void
    {
        $product = Product::factory()->create(['name' => 'Producto Original']);

        $this->browse(function (Browser $browser) use ($product) {
            $browser->loginAs($this->gestor)
                ->visit('/products/' . $product->id . '/edit')
                ->clear('name')
                ->type('name', 'Producto Editado')
                ->press('Actualizar')
                ->assertPathIs('/products')
                ->assertSee('Producto actualizado exitosamente');
        });
    }

    public function test_usuario_puede_ver_detalle_producto(): void
    {
        $product = Product::factory()->create(['name' => 'Producto Detalle']);

        $this->browse(function (Browser $browser) use ($product) {
            $browser->loginAs($this->gestor)
                ->visit('/products/' . $product->id)
                ->assertSee('Producto Detalle')
                ->assertSee('Stock');
        });
    }

    public function test_formulario_muestra_campos_bobina_cuando_seleccionado(): void
    {
        ProductType::factory()->create();
        Proveedor::factory()->create();
        Unidad::factory()->create();

        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->gestor)
                ->visit('/products/create')
                ->check('usa_bobina')
                ->assertVisible('input[name="ancho"]')
                ->assertVisible('input[name="largo"]')
                ->assertVisible('input[name="fuelle"]');
        });
    }

    public function test_paginacion_funciona_correctamente(): void
    {
        Product::factory()->count(20)->create();

        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->gestor)
                ->visit('/products')
                ->assertPresent('.pagination')
                ->click('.pagination a[rel="next"]')
                ->assertQueryStringHas('page', '2');
        });
    }
}
