<?php

namespace Tests\Browser;

use App\Models\User;
use App\Models\Sale;
use App\Models\Client;
use App\Models\Product;
use App\Models\ProductPrice;
use App\Models\Transport;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class SaleTest extends DuskTestCase
{
    use DatabaseMigrations;

    private User $vendedor;

    protected function setUp(): void
    {
        parent::setUp();
        $this->vendedor = User::factory()->create(['role' => 'vendedor']);
    }

    public function test_vendedor_puede_ver_lista_ventas(): void
    {
        Sale::factory()->count(3)->create();

        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->vendedor)
                ->visit('/sales')
                ->assertSee('Ventas')
                ->assertPresent('table');
        });
    }

    public function test_vendedor_puede_navegar_a_crear_venta(): void
    {
        Client::factory()->create();
        $product = Product::factory()->create(['stock_actual' => 100]);
        ProductPrice::factory()->create(['product_id' => $product->id]);
        Transport::factory()->create();

        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->vendedor)
                ->visit('/sales')
                ->clickLink('Nueva Venta')
                ->assertPathIs('/sales/create')
                ->assertSee('Crear Venta');
        });
    }

    public function test_formulario_venta_muestra_productos_con_stock(): void
    {
        Client::factory()->create();
        $productConStock = Product::factory()->create([
            'name' => 'Producto Con Stock',
            'stock_actual' => 100,
        ]);
        ProductPrice::factory()->create(['product_id' => $productConStock->id]);

        $productSinStock = Product::factory()->create([
            'name' => 'Producto Sin Stock',
            'stock_actual' => 0,
        ]);
        ProductPrice::factory()->create(['product_id' => $productSinStock->id]);

        Transport::factory()->create();

        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->vendedor)
                ->visit('/sales/create')
                ->assertSee('Producto Con Stock')
                ->assertDontSee('Producto Sin Stock');
        });
    }

    public function test_vendedor_puede_ver_detalle_venta(): void
    {
        $sale = Sale::factory()->create();

        $this->browse(function (Browser $browser) use ($sale) {
            $browser->loginAs($this->vendedor)
                ->visit('/sales/' . $sale->id)
                ->assertSee('Detalle de Venta')
                ->assertSee('Total');
        });
    }

    public function test_no_puede_editar_venta_existente(): void
    {
        $sale = Sale::factory()->create();

        $this->browse(function (Browser $browser) use ($sale) {
            $browser->loginAs($this->vendedor)
                ->visit('/sales/' . $sale->id . '/edit')
                ->assertPathIs('/sales')
                ->assertSee('No se pueden editar ventas ya registradas');
        });
    }

    public function test_total_se_actualiza_dinamicamente(): void
    {
        $client = Client::factory()->create();
        $product = Product::factory()->create(['stock_actual' => 100]);
        $price = ProductPrice::factory()->create([
            'product_id' => $product->id,
            'precio_venta' => 100,
        ]);
        $transport = Transport::factory()->create(['costo' => 50]);

        $this->browse(function (Browser $browser) use ($product, $transport) {
            $browser->loginAs($this->vendedor)
                ->visit('/sales/create')
                ->select('transport_id', $transport->id)
                ->waitFor('#total')
                ->assertSeeIn('#total', '50');
        });
    }
}
