<?php

namespace Tests\Unit\Models;

use App\Models\Canal;
use App\Models\Client;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CanalTest extends TestCase
{
    use RefreshDatabase;

    public function test_puede_crear_canal(): void
    {
        $canal = Canal::factory()->create();

        $this->assertDatabaseHas('canales', [
            'id' => $canal->id,
            'descripcion' => $canal->descripcion,
        ]);
    }

    public function test_canal_tiene_muchos_clientes(): void
    {
        $canal = Canal::factory()->create();
        Client::factory()->count(3)->create(['canal_id' => $canal->id]);

        $this->assertCount(3, $canal->clients);
        $this->assertInstanceOf(Client::class, $canal->clients->first());
    }

    public function test_usa_tabla_canales(): void
    {
        $canal = new Canal();

        $this->assertEquals('canales', $canal->getTable());
    }
}
