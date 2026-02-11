<?php

namespace Tests\Unit\Models;

use App\Models\Client;
use App\Models\Canal;
use App\Models\Sale;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ClientTest extends TestCase
{
    use RefreshDatabase;

    public function test_puede_crear_cliente(): void
    {
        $client = Client::factory()->create();

        $this->assertDatabaseHas('clients', [
            'id' => $client->id,
            'nombre' => $client->nombre,
        ]);
    }

    public function test_cliente_pertenece_a_canal(): void
    {
        $canal = Canal::factory()->create();
        $client = Client::factory()->create(['canal_id' => $canal->id]);

        $this->assertInstanceOf(Canal::class, $client->canal);
        $this->assertEquals($canal->id, $client->canal->id);
    }

    public function test_cliente_tiene_muchas_ventas(): void
    {
        $client = Client::factory()->create();
        Sale::factory()->count(3)->create(['client_id' => $client->id]);

        $this->assertCount(3, $client->sales);
        $this->assertInstanceOf(Sale::class, $client->sales->first());
    }

    public function test_cliente_pertenece_a_creador(): void
    {
        $user = User::factory()->create();
        $client = Client::factory()->create(['created_by' => $user->id]);

        $this->assertInstanceOf(User::class, $client->creator);
        $this->assertEquals($user->id, $client->creator->id);
    }

    public function test_cliente_fillable_correcto(): void
    {
        $data = [
            'nombre' => 'Juan Pérez',
            'telefono' => '123456789',
            'email' => 'juan@test.com',
            'direccion' => 'Calle 123',
            'canal_id' => Canal::factory()->create()->id,
            'created_by' => User::factory()->create()->id,
            'eliminado' => false,
        ];

        $client = Client::create($data);

        $this->assertEquals('Juan Pérez', $client->nombre);
        $this->assertEquals('juan@test.com', $client->email);
    }
}
