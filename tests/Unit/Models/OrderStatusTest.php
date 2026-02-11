<?php

namespace Tests\Unit\Models;

use App\Models\OrderStatus;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrderStatusTest extends TestCase
{
    use RefreshDatabase;

    public function test_puede_crear_estado(): void
    {
        $status = OrderStatus::factory()->create();

        $this->assertDatabaseHas('order_status', [
            'id' => $status->id,
            'nombre' => $status->nombre,
        ]);
    }

    public function test_estado_pertenece_a_creador(): void
    {
        $user = User::factory()->create();
        $status = OrderStatus::factory()->create(['created_by' => $user->id]);

        $this->assertInstanceOf(User::class, $status->creator);
        $this->assertEquals($user->id, $status->creator->id);
    }

    public function test_usa_tabla_order_status(): void
    {
        $status = new OrderStatus();

        $this->assertEquals('order_status', $status->getTable());
    }
}
