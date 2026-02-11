<?php

namespace Tests\Unit\Models;

use App\Models\Transport;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TransportTest extends TestCase
{
    use RefreshDatabase;

    public function test_puede_crear_transporte(): void
    {
        $transport = Transport::factory()->create();

        $this->assertDatabaseHas('transports', [
            'id' => $transport->id,
            'nombre' => $transport->nombre,
        ]);
    }

    public function test_transporte_pertenece_a_creador(): void
    {
        $user = User::factory()->create();
        $transport = Transport::factory()->create(['created_by' => $user->id]);

        $this->assertInstanceOf(User::class, $transport->creator);
        $this->assertEquals($user->id, $transport->creator->id);
    }

    public function test_cast_costo_decimal(): void
    {
        $transport = Transport::factory()->create(['costo' => 1500.75]);

        $this->assertEquals('1500.75', $transport->costo);
    }
}
