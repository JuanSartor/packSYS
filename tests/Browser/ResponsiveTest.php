<?php

namespace Tests\Browser;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class ResponsiveTest extends DuskTestCase
{
    use DatabaseMigrations;

    public function test_login_funciona_en_movil(): void
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('password123'),
        ]);

        $this->browse(function (Browser $browser) use ($user) {
            $browser->resize(375, 667) // iPhone 6/7/8 dimensions
                ->visit('/login')
                ->type('email', 'test@example.com')
                ->type('password', 'password123')
                ->press('Log in')
                ->assertPathIs('/dashboard');
        });
    }

    public function test_menu_hamburguesa_visible_en_movil(): void
    {
        $user = User::factory()->create();

        $this->browse(function (Browser $browser) use ($user) {
            $browser->loginAs($user)
                ->resize(375, 667)
                ->visit('/dashboard')
                ->assertPresent('[data-mobile-menu], .hamburger-menu, button[aria-label*="menu"]');
        });
    }

    public function test_tabla_productos_es_responsive(): void
    {
        $user = User::factory()->create(['role' => 'gestor']);

        $this->browse(function (Browser $browser) use ($user) {
            $browser->loginAs($user)
                ->resize(375, 667)
                ->visit('/products')
                ->assertPresent('table, .responsive-table, [data-table]');
        });
    }

    public function test_formulario_se_adapta_a_tablet(): void
    {
        $user = User::factory()->create(['role' => 'gestor']);

        $this->browse(function (Browser $browser) use ($user) {
            $browser->loginAs($user)
                ->resize(768, 1024) // iPad dimensions
                ->visit('/products/create')
                ->assertPresent('form')
                ->assertPresent('input[name="name"]');
        });
    }

    public function test_dashboard_responsive_en_diferentes_resoluciones(): void
    {
        $user = User::factory()->create();

        $resolutions = [
            [320, 568],   // iPhone 5
            [375, 667],   // iPhone 6/7/8
            [414, 896],   // iPhone XR
            [768, 1024],  // iPad
            [1024, 768],  // iPad Landscape
            [1280, 800],  // Laptop
            [1920, 1080], // Desktop
        ];

        $this->browse(function (Browser $browser) use ($user, $resolutions) {
            foreach ($resolutions as $resolution) {
                $browser->loginAs($user)
                    ->resize($resolution[0], $resolution[1])
                    ->visit('/dashboard')
                    ->assertPresent('nav, .navigation, [role="navigation"]')
                    ->assertSee('Dashboard');
            }
        });
    }
}
