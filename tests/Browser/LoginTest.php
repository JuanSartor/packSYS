<?php

namespace Tests\Browser;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class LoginTest extends DuskTestCase
{
    use DatabaseMigrations;

    public function test_usuario_puede_ver_formulario_login(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/login')
                ->assertSee('Email')
                ->assertSee('Password')
                ->assertPresent('input[name="email"]')
                ->assertPresent('input[name="password"]');
        });
    }

    public function test_usuario_puede_iniciar_sesion(): void
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('password123'),
        ]);

        $this->browse(function (Browser $browser) use ($user) {
            $browser->visit('/login')
                ->type('email', 'test@example.com')
                ->type('password', 'password123')
                ->press('Log in')
                ->assertPathIs('/dashboard')
                ->assertAuthenticated();
        });
    }

    public function test_usuario_con_credenciales_incorrectas_ve_error(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/login')
                ->type('email', 'noexiste@example.com')
                ->type('password', 'wrongpassword')
                ->press('Log in')
                ->assertPathIs('/login')
                ->assertSee('credentials');
        });
    }

    public function test_usuario_puede_cerrar_sesion(): void
    {
        $user = User::factory()->create();

        $this->browse(function (Browser $browser) use ($user) {
            $browser->loginAs($user)
                ->visit('/dashboard')
                ->click('@user-menu')
                ->clickLink('Log Out')
                ->assertPathIs('/');
        });
    }
}
