<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\Client;
use App\Models\Product;
use App\Models\ProductPrice;
use App\Models\Transport;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SaleControllerTest extends TestCase
{
    use RefreshDatabase;

    private User $gestor;
    private User $vendedor;
    private User $operario;

    protected function setUp(): void
    {
        parent::setUp();

        $this->gestor = User::factory()->create(['role' => 'gestor']);
        $this->vendedor = User::factory()->create(['role' => 'vendedor']);
        $this->operario = User::factory()->create(['role' => 'operario']);
    }

    public function test_usuario_no_autenticado_no_puede_ver_ventas(): void
    {
        $response = $this->get(route('sales.index'));

        $response->assertRedirect(route('login'));
    }

    public function test_vendedor_puede_ver_lista_ventas(): void
    {
        Sale::factory()->count(3)->create();

        $response = $this->actingAs($this->vendedor)->get(route('sales.index'));

        $response->assertStatus(200);
        $response->assertViewIs('sales.index');
    }

    public function test_operario_no_puede_ver_ventas(): void
    {
        $response = $this->actingAs($this->operario)->get(route('sales.index'));

        $response->assertStatus(403);
    }

    public function test_lista_no_muestra_ventas_eliminadas(): void
    {
        $ventaActiva = Sale::factory()->create(['eliminado' => false]);
        $ventaEliminada = Sale::factory()->create(['eliminado' => true]);

        $response = $this->actingAs($this->vendedor)->get(route('sales.index'));

        $sales = $response->viewData('sales');
        $this->assertTrue($sales->contains('id', $ventaActiva->id));
        $this->assertFalse($sales->contains('id', $ventaEliminada->id));
    }

    public function test_vendedor_puede_ver_formulario_crear_venta(): void
    {
        Client::factory()->create();
        $product = Product::factory()->create(['stock_actual' => 100]);
        ProductPrice::factory()->create(['product_id' => $product->id]);
        Transport::factory()->create();

        $response = $this->actingAs($this->vendedor)->get(route('sales.create'));

        $response->assertStatus(200);
        $response->assertViewIs('sales.create');
        $response->assertViewHas(['clients', 'products', 'transports', 'productsData']);
    }

    public function test_vendedor_puede_crear_venta(): void
    {
        $client = Client::factory()->create();
        $transport = Transport::factory()->create(['costo' => 100]);
        $product = Product::factory()->create(['stock_actual' => 100]);
        $price = ProductPrice::factory()->create([
            'product_id' => $product->id,
            'precio_venta' => 50,
        ]);

        $data = [
            'client_id' => $client->id,
            'transport_id' => $transport->id,
            'items' => [
                [
                    'product_id' => $product->id,
                    'product_price_id' => $price->id,
                    'cantidad' => 10,
                    'precio_unitario' => 50,
                ],
            ],
        ];

        $response = $this->actingAs($this->vendedor)->post(route('sales.store'), $data);

        $response->assertRedirect(route('sales.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('sales', [
            'client_id' => $client->id,
            'transport_id' => $transport->id,
            'total' => 600, // (10 * 50) + 100 transporte
        ]);

        // Verificar que se restó el stock
        $this->assertEquals(90, $product->fresh()->stock_actual);
    }

    public function test_no_puede_crear_venta_sin_stock_suficiente(): void
    {
        $client = Client::factory()->create();
        $transport = Transport::factory()->create();
        $product = Product::factory()->create(['stock_actual' => 5]);
        $price = ProductPrice::factory()->create(['product_id' => $product->id]);

        $data = [
            'client_id' => $client->id,
            'transport_id' => $transport->id,
            'items' => [
                [
                    'product_id' => $product->id,
                    'product_price_id' => $price->id,
                    'cantidad' => 10,
                    'precio_unitario' => 50,
                ],
            ],
        ];

        $response = $this->actingAs($this->vendedor)->post(route('sales.store'), $data);

        $response->assertSessionHas('error');
    }

    public function test_validacion_cliente_requerido(): void
    {
        $transport = Transport::factory()->create();
        $product = Product::factory()->create(['stock_actual' => 100]);

        $data = [
            'transport_id' => $transport->id,
            'items' => [
                [
                    'product_id' => $product->id,
                    'cantidad' => 10,
                    'precio_unitario' => 50,
                ],
            ],
        ];

        $response = $this->actingAs($this->vendedor)->post(route('sales.store'), $data);

        $response->assertSessionHasErrors('client_id');
    }

    public function test_validacion_transporte_requerido(): void
    {
        $client = Client::factory()->create();
        $product = Product::factory()->create(['stock_actual' => 100]);

        $data = [
            'client_id' => $client->id,
            'items' => [
                [
                    'product_id' => $product->id,
                    'cantidad' => 10,
                    'precio_unitario' => 50,
                ],
            ],
        ];

        $response = $this->actingAs($this->vendedor)->post(route('sales.store'), $data);

        $response->assertSessionHasErrors('transport_id');
    }

    public function test_validacion_items_requeridos(): void
    {
        $client = Client::factory()->create();
        $transport = Transport::factory()->create();

        $data = [
            'client_id' => $client->id,
            'transport_id' => $transport->id,
            'items' => [],
        ];

        $response = $this->actingAs($this->vendedor)->post(route('sales.store'), $data);

        $response->assertSessionHasErrors('items');
    }

    public function test_puede_ver_detalle_venta(): void
    {
        $sale = Sale::factory()->create();

        $response = $this->actingAs($this->vendedor)->get(route('sales.show', $sale));

        $response->assertStatus(200);
        $response->assertViewIs('sales.show');
    }

    public function test_no_puede_editar_venta(): void
    {
        $sale = Sale::factory()->create();

        $response = $this->actingAs($this->vendedor)->get(route('sales.edit', $sale));

        $response->assertRedirect(route('sales.index'));
        $response->assertSessionHas('error');
    }

    public function test_puede_eliminar_venta(): void
    {
        $sale = Sale::factory()->create(['eliminado' => false]);

        $response = $this->actingAs($this->vendedor)->delete(route('sales.destroy', $sale));

        $response->assertRedirect(route('sales.index'));
        $this->assertTrue($sale->fresh()->eliminado == 1);
    }

    public function test_crear_venta_genera_movimiento_stock(): void
    {
        $client = Client::factory()->create();
        $transport = Transport::factory()->create();
        $product = Product::factory()->create(['stock_actual' => 100]);
        $price = ProductPrice::factory()->create(['product_id' => $product->id]);

        $data = [
            'client_id' => $client->id,
            'transport_id' => $transport->id,
            'items' => [
                [
                    'product_id' => $product->id,
                    'product_price_id' => $price->id,
                    'cantidad' => 10,
                    'precio_unitario' => 50,
                ],
            ],
        ];

        $this->actingAs($this->vendedor)->post(route('sales.store'), $data);

        $this->assertDatabaseHas('stock_movements', [
            'product_id' => $product->id,
            'tipo' => 'salida',
            'cantidad' => 10,
        ]);
    }
}
