<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Client;
use App\Models\Canal;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ClientControllerTest extends TestCase
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

    public function test_usuario_no_autenticado_no_puede_ver_clientes(): void
    {
        $response = $this->get(route('clients.index'));

        $response->assertRedirect(route('login'));
    }

    public function test_gestor_puede_ver_lista_clientes(): void
    {
        Client::factory()->count(3)->create();

        $response = $this->actingAs($this->gestor)->get(route('clients.index'));

        $response->assertStatus(200);
        $response->assertViewIs('clients.index');
    }

    public function test_vendedor_puede_ver_lista_clientes(): void
    {
        Client::factory()->count(3)->create();

        $response = $this->actingAs($this->vendedor)->get(route('clients.index'));

        $response->assertStatus(200);
    }

    public function test_operario_no_puede_ver_clientes(): void
    {
        $response = $this->actingAs($this->operario)->get(route('clients.index'));

        $response->assertStatus(403);
    }

    public function test_lista_no_muestra_clientes_eliminados(): void
    {
        $clienteActivo = Client::factory()->create(['eliminado' => false]);
        $clienteEliminado = Client::factory()->create(['eliminado' => true]);

        $response = $this->actingAs($this->gestor)->get(route('clients.index'));

        $clients = $response->viewData('clients');
        $this->assertTrue($clients->contains('id', $clienteActivo->id));
        $this->assertFalse($clients->contains('id', $clienteEliminado->id));
    }

    public function test_vendedor_puede_ver_formulario_crear_cliente(): void
    {
        Canal::factory()->create();

        $response = $this->actingAs($this->vendedor)->get(route('clients.create'));

        $response->assertStatus(200);
        $response->assertViewIs('clients.create');
        $response->assertViewHas('canales');
    }

    public function test_vendedor_puede_crear_cliente(): void
    {
        $canal = Canal::factory()->create();

        $data = [
            'nombre' => 'Juan Pérez',
            'telefono' => '123456789',
            'email' => 'juan@test.com',
            'direccion' => 'Calle 123',
            'canal_id' => $canal->id,
        ];

        $response = $this->actingAs($this->vendedor)->post(route('clients.store'), $data);

        $response->assertRedirect(route('clients.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('clients', [
            'nombre' => 'Juan Pérez',
            'email' => 'juan@test.com',
            'created_by' => $this->vendedor->id,
        ]);
    }

    public function test_validacion_nombre_requerido(): void
    {
        $canal = Canal::factory()->create();

        $data = [
            'nombre' => '',
            'canal_id' => $canal->id,
        ];

        $response = $this->actingAs($this->vendedor)->post(route('clients.store'), $data);

        $response->assertSessionHasErrors('nombre');
    }

    public function test_validacion_canal_requerido(): void
    {
        $data = [
            'nombre' => 'Juan Pérez',
        ];

        $response = $this->actingAs($this->vendedor)->post(route('clients.store'), $data);

        $response->assertSessionHasErrors('canal_id');
    }

    public function test_validacion_email_formato(): void
    {
        $canal = Canal::factory()->create();

        $data = [
            'nombre' => 'Juan Pérez',
            'email' => 'email-invalido',
            'canal_id' => $canal->id,
        ];

        $response = $this->actingAs($this->vendedor)->post(route('clients.store'), $data);

        $response->assertSessionHasErrors('email');
    }

    public function test_puede_ver_detalle_cliente(): void
    {
        $client = Client::factory()->create();

        $response = $this->actingAs($this->vendedor)->get(route('clients.show', $client));

        $response->assertStatus(200);
        $response->assertViewIs('clients.show');
    }

    public function test_puede_editar_cliente(): void
    {
        $client = Client::factory()->create();

        $response = $this->actingAs($this->vendedor)->get(route('clients.edit', $client));

        $response->assertStatus(200);
        $response->assertViewIs('clients.edit');
    }

    public function test_puede_actualizar_cliente(): void
    {
        $client = Client::factory()->create();
        $canal = Canal::factory()->create();

        $data = [
            'nombre' => 'Cliente Actualizado',
            'telefono' => '987654321',
            'email' => 'actualizado@test.com',
            'direccion' => 'Nueva Dirección',
            'canal_id' => $canal->id,
        ];

        $response = $this->actingAs($this->vendedor)->put(route('clients.update', $client), $data);

        $response->assertRedirect(route('clients.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('clients', [
            'id' => $client->id,
            'nombre' => 'Cliente Actualizado',
        ]);
    }

    public function test_puede_eliminar_cliente(): void
    {
        $client = Client::factory()->create(['eliminado' => false]);

        $response = $this->actingAs($this->vendedor)->delete(route('clients.destroy', $client));

        $response->assertRedirect(route('clients.index'));
        $this->assertTrue($client->fresh()->eliminado == 1);
    }
}
