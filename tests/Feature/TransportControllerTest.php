<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Transport;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TransportControllerTest extends TestCase
{
    use RefreshDatabase;

    private User $gestor;
    private User $vendedor;

    protected function setUp(): void
    {
        parent::setUp();

        $this->gestor = User::factory()->create(['role' => 'gestor']);
        $this->vendedor = User::factory()->create(['role' => 'vendedor']);
    }

    public function test_gestor_puede_ver_lista_transportes(): void
    {
        Transport::factory()->count(3)->create();

        $response = $this->actingAs($this->gestor)->get(route('transports.index'));

        $response->assertStatus(200);
        $response->assertViewIs('transports.index');
    }

    public function test_vendedor_no_puede_ver_transportes(): void
    {
        $response = $this->actingAs($this->vendedor)->get(route('transports.index'));

        $response->assertStatus(403);
    }

    public function test_gestor_puede_crear_transporte(): void
    {
        $data = [
            'nombre' => 'Transporte Rápido',
            'costo' => 500.00,
        ];

        $response = $this->actingAs($this->gestor)->post(route('transports.store'), $data);

        $response->assertRedirect(route('transports.index'));

        $this->assertDatabaseHas('transports', [
            'nombre' => 'Transporte Rápido',
            'costo' => 500.00,
            'created_by' => $this->gestor->id,
        ]);
    }

    public function test_gestor_puede_actualizar_transporte(): void
    {
        $transport = Transport::factory()->create();

        $data = [
            'nombre' => 'Transporte Actualizado',
            'costo' => 750.00,
        ];

        $response = $this->actingAs($this->gestor)->put(route('transports.update', $transport), $data);

        $response->assertRedirect(route('transports.index'));

        $this->assertDatabaseHas('transports', [
            'id' => $transport->id,
            'nombre' => 'Transporte Actualizado',
        ]);
    }

    public function test_gestor_puede_eliminar_transporte(): void
    {
        $transport = Transport::factory()->create(['eliminado' => false]);

        $response = $this->actingAs($this->gestor)->delete(route('transports.destroy', $transport));

        $response->assertRedirect(route('transports.index'));
        $this->assertTrue($transport->fresh()->eliminado == 1);
    }

    public function test_validacion_nombre_requerido(): void
    {
        $data = [
            'costo' => 500.00,
        ];

        $response = $this->actingAs($this->gestor)->post(route('transports.store'), $data);

        $response->assertSessionHasErrors('nombre');
    }
}
