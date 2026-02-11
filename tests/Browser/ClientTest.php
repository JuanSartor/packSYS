<?php

namespace Tests\Browser;

use App\Models\User;
use App\Models\Client;
use App\Models\Canal;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class ClientTest extends DuskTestCase
{
    use DatabaseMigrations;

    private User $vendedor;

    protected function setUp(): void
    {
        parent::setUp();
        $this->vendedor = User::factory()->create(['role' => 'vendedor']);
    }

    public function test_vendedor_puede_ver_lista_clientes(): void
    {
        Client::factory()->count(3)->create();

        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->vendedor)
                ->visit('/clients')
                ->assertSee('Clientes')
                ->assertPresent('table');
        });
    }

    public function test_vendedor_puede_crear_cliente_mediante_formulario(): void
    {
        $canal = Canal::factory()->create(['descripcion' => 'Facebook']);

        $this->browse(function (Browser $browser) use ($canal) {
            $browser->loginAs($this->vendedor)
                ->visit('/clients/create')
                ->type('nombre', 'Juan Perez')
                ->type('telefono', '123456789')
                ->type('email', 'juan@test.com')
                ->type('direccion', 'Calle 123')
                ->select('canal_id', $canal->id)
                ->press('Guardar')
                ->assertPathIs('/clients')
                ->assertSee('Cliente creado exitosamente');
        });
    }

    public function test_formulario_valida_email_formato(): void
    {
        $canal = Canal::factory()->create();

        $this->browse(function (Browser $browser) use ($canal) {
            $browser->loginAs($this->vendedor)
                ->visit('/clients/create')
                ->type('nombre', 'Juan Perez')
                ->type('email', 'email-invalido')
                ->select('canal_id', $canal->id)
                ->press('Guardar')
                ->assertPathIs('/clients/create')
                ->assertSee('email');
        });
    }

    public function test_vendedor_puede_editar_cliente(): void
    {
        $client = Client::factory()->create(['nombre' => 'Cliente Original']);

        $this->browse(function (Browser $browser) use ($client) {
            $browser->loginAs($this->vendedor)
                ->visit('/clients/' . $client->id . '/edit')
                ->clear('nombre')
                ->type('nombre', 'Cliente Editado')
                ->press('Actualizar')
                ->assertPathIs('/clients')
                ->assertSee('Cliente actualizado exitosamente');
        });
    }

    public function test_vendedor_puede_ver_detalle_cliente(): void
    {
        $client = Client::factory()->create(['nombre' => 'Cliente Detalle']);

        $this->browse(function (Browser $browser) use ($client) {
            $browser->loginAs($this->vendedor)
                ->visit('/clients/' . $client->id)
                ->assertSee('Cliente Detalle');
        });
    }

    public function test_vendedor_puede_eliminar_cliente(): void
    {
        $client = Client::factory()->create(['nombre' => 'Cliente a Eliminar']);

        $this->browse(function (Browser $browser) use ($client) {
            $browser->loginAs($this->vendedor)
                ->visit('/clients')
                ->assertSee('Cliente a Eliminar')
                ->press('@delete-client-' . $client->id)
                ->acceptDialog()
                ->assertSee('Cliente eliminado exitosamente')
                ->assertDontSee('Cliente a Eliminar');
        });
    }
}
