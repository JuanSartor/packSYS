<?php

namespace Tests\Browser;

use App\Models\User;
use App\Models\Product;
use App\Models\ProductionOrder;
use App\Models\OrderStatus;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class ProductionOrderTest extends DuskTestCase
{
    use DatabaseMigrations;

    private User $operario;
    private User $gestor;

    protected function setUp(): void
    {
        parent::setUp();
        $this->operario = User::factory()->create(['role' => 'operario']);
        $this->gestor = User::factory()->create(['role' => 'gestor']);
    }

    public function test_operario_puede_ver_lista_ordenes(): void
    {
        ProductionOrder::factory()->count(3)->create();

        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->operario)
                ->visit('/production-orders')
                ->assertSee('Ordenes de Produccion')
                ->assertPresent('table');
        });
    }

    public function test_gestor_puede_crear_orden_produccion(): void
    {
        $product = Product::factory()->create(['name' => 'Producto Test']);
        $status = OrderStatus::factory()->create(['nombre' => 'Pendiente']);

        $this->browse(function (Browser $browser) use ($product, $status) {
            $browser->loginAs($this->gestor)
                ->visit('/production-orders/create')
                ->select('product_id', $product->id)
                ->type('cantidad', '100')
                ->select('order_status_id', $status->id)
                ->press('Guardar')
                ->assertPathIs('/production-orders')
                ->assertSee('Orden de produccion creada exitosamente');
        });
    }

    public function test_operario_puede_iniciar_orden(): void
    {
        $order = ProductionOrder::factory()->create([
            'estado' => 'pendiente',
            'started_at' => null,
        ]);

        $this->browse(function (Browser $browser) use ($order) {
            $browser->loginAs($this->operario)
                ->visit('/production-orders/' . $order->id)
                ->press('Iniciar')
                ->assertSee('Orden iniciada');
        });
    }

    public function test_operario_puede_pausar_orden(): void
    {
        $order = ProductionOrder::factory()->enProceso()->create();

        $this->browse(function (Browser $browser) use ($order) {
            $browser->loginAs($this->operario)
                ->visit('/production-orders/' . $order->id)
                ->press('Pausar')
                ->assertSee('Orden pausada');
        });
    }

    public function test_operario_puede_finalizar_orden(): void
    {
        $order = ProductionOrder::factory()->enProceso()->create();

        $this->browse(function (Browser $browser) use ($order) {
            $browser->loginAs($this->operario)
                ->visit('/production-orders/' . $order->id)
                ->press('Finalizar')
                ->assertSee('Orden finalizada');
        });
    }

    public function test_muestra_tiempo_transcurrido(): void
    {
        $order = ProductionOrder::factory()->enProceso()->create([
            'started_at' => now()->subHours(2),
        ]);

        $this->browse(function (Browser $browser) use ($order) {
            $browser->loginAs($this->operario)
                ->visit('/production-orders/' . $order->id)
                ->assertSee('Tiempo');
        });
    }

    public function test_puede_ver_historial_tiempos(): void
    {
        $order = ProductionOrder::factory()->completado()->create();

        $this->browse(function (Browser $browser) use ($order) {
            $browser->loginAs($this->operario)
                ->visit('/production-orders/' . $order->id)
                ->assertSee('Historial de tiempos');
        });
    }
}
