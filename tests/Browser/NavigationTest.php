<?php

namespace Tests\Browser;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class NavigationTest extends DuskTestCase
{
    use DatabaseMigrations;

    public function test_gestor_puede_ver_todos_los_menus(): void
    {
        $gestor = User::factory()->create(['role' => 'gestor']);

        $this->browse(function (Browser $browser) use ($gestor) {
            $browser->loginAs($gestor)
                ->visit('/dashboard')
                ->assertSee('Dashboard')
                ->assertSee('Productos')
                ->assertSee('Clientes')
                ->assertSee('Ventas')
                ->assertSee('Ordenes')
                ->assertSee('Bobinas')
                ->assertSee('Usuarios');
        });
    }

    public function test_vendedor_ve_menus_limitados(): void
    {
        $vendedor = User::factory()->create(['role' => 'vendedor']);

        $this->browse(function (Browser $browser) use ($vendedor) {
            $browser->loginAs($vendedor)
                ->visit('/dashboard')
                ->assertSee('Dashboard')
                ->assertSee('Productos')
                ->assertSee('Clientes')
                ->assertSee('Ventas')
                ->assertDontSee('Usuarios');
        });
    }

    public function test_operario_ve_menus_limitados(): void
    {
        $operario = User::factory()->create(['role' => 'operario']);

        $this->browse(function (Browser $browser) use ($operario) {
            $browser->loginAs($operario)
                ->visit('/dashboard')
                ->assertSee('Dashboard')
                ->assertSee('Ordenes')
                ->assertSee('Bobinas')
                ->assertDontSee('Usuarios')
                ->assertDontSee('Ventas');
        });
    }

    public function test_navegacion_desde_dashboard_a_productos(): void
    {
        $gestor = User::factory()->create(['role' => 'gestor']);

        $this->browse(function (Browser $browser) use ($gestor) {
            $browser->loginAs($gestor)
                ->visit('/dashboard')
                ->clickLink('Productos')
                ->assertPathIs('/products');
        });
    }

    public function test_navegacion_desde_dashboard_a_clientes(): void
    {
        $vendedor = User::factory()->create(['role' => 'vendedor']);

        $this->browse(function (Browser $browser) use ($vendedor) {
            $browser->loginAs($vendedor)
                ->visit('/dashboard')
                ->clickLink('Clientes')
                ->assertPathIs('/clients');
        });
    }

    public function test_navegacion_desde_dashboard_a_ventas(): void
    {
        $vendedor = User::factory()->create(['role' => 'vendedor']);

        $this->browse(function (Browser $browser) use ($vendedor) {
            $browser->loginAs($vendedor)
                ->visit('/dashboard')
                ->clickLink('Ventas')
                ->assertPathIs('/sales');
        });
    }

    public function test_breadcrumbs_funcionan_correctamente(): void
    {
        $gestor = User::factory()->create(['role' => 'gestor']);

        $this->browse(function (Browser $browser) use ($gestor) {
            $browser->loginAs($gestor)
                ->visit('/products/create')
                ->assertSee('Productos')
                ->clickLink('Productos')
                ->assertPathIs('/products');
        });
    }

    public function test_logo_redirige_a_dashboard(): void
    {
        $user = User::factory()->create();

        $this->browse(function (Browser $browser) use ($user) {
            $browser->loginAs($user)
                ->visit('/products')
                ->click('.logo, [href="/dashboard"]')
                ->assertPathIs('/dashboard');
        });
    }
}
