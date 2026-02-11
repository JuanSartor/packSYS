<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\PaperCoil;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PaperCoilControllerTest extends TestCase
{
    use RefreshDatabase;

    private User $gestor;
    private User $operario;
    private User $vendedor;

    protected function setUp(): void
    {
        parent::setUp();

        $this->gestor = User::factory()->create(['role' => 'gestor']);
        $this->operario = User::factory()->create(['role' => 'operario']);
        $this->vendedor = User::factory()->create(['role' => 'vendedor']);
    }

    public function test_gestor_puede_ver_lista_bobinas(): void
    {
        PaperCoil::factory()->count(3)->create();

        $response = $this->actingAs($this->gestor)->get(route('paper-coils.index'));

        $response->assertStatus(200);
        $response->assertViewIs('paper-coils.index');
    }

    public function test_operario_puede_ver_lista_bobinas(): void
    {
        $response = $this->actingAs($this->operario)->get(route('paper-coils.index'));

        $response->assertStatus(200);
    }

    public function test_vendedor_no_puede_ver_bobinas(): void
    {
        $response = $this->actingAs($this->vendedor)->get(route('paper-coils.index'));

        $response->assertStatus(403);
    }

    public function test_puede_crear_bobina(): void
    {
        $data = [
            'tipo_papel' => 'Kraft',
            'ancho' => 100,
            'gramaje' => 80,
            'peso_inicial' => 500,
            'peso_actual' => 500,
            'alerta_minima' => 50,
        ];

        $response = $this->actingAs($this->gestor)->post(route('paper-coils.store'), $data);

        $response->assertRedirect(route('paper-coils.index'));

        $this->assertDatabaseHas('paper_coils', [
            'tipo_papel' => 'Kraft',
            'ancho' => 100,
        ]);
    }

    public function test_puede_ver_detalle_bobina(): void
    {
        $coil = PaperCoil::factory()->create();

        $response = $this->actingAs($this->operario)->get(route('paper-coils.show', $coil));

        $response->assertStatus(200);
        $response->assertViewIs('paper-coils.show');
    }

    public function test_puede_actualizar_bobina(): void
    {
        $coil = PaperCoil::factory()->create();

        $data = [
            'tipo_papel' => 'Blanco',
            'ancho' => 120,
            'gramaje' => 90,
            'peso_inicial' => $coil->peso_inicial,
            'peso_actual' => 300,
            'alerta_minima' => 30,
        ];

        $response = $this->actingAs($this->gestor)->put(route('paper-coils.update', $coil), $data);

        $response->assertRedirect(route('paper-coils.index'));

        $this->assertDatabaseHas('paper_coils', [
            'id' => $coil->id,
            'tipo_papel' => 'Blanco',
        ]);
    }

    public function test_puede_eliminar_bobina(): void
    {
        $coil = PaperCoil::factory()->create(['eliminado' => false]);

        $response = $this->actingAs($this->gestor)->delete(route('paper-coils.destroy', $coil));

        $response->assertRedirect(route('paper-coils.index'));
        $this->assertTrue($coil->fresh()->eliminado == 1);
    }

    public function test_validacion_tipo_papel_requerido(): void
    {
        $data = [
            'ancho' => 100,
            'gramaje' => 80,
            'peso_inicial' => 500,
            'peso_actual' => 500,
            'alerta_minima' => 50,
        ];

        $response = $this->actingAs($this->gestor)->post(route('paper-coils.store'), $data);

        $response->assertSessionHasErrors('tipo_papel');
    }
}
