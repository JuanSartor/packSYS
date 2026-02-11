<?php

namespace Tests\Unit\Models;

use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\Client;
use App\Models\Transport;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SaleTest extends TestCase
{
    use RefreshDatabase;

    public function test_puede_crear_venta(): void
    {
        $sale = Sale::factory()->create();

        $this->assertDatabaseHas('sales', [
            'id' => $sale->id,
        ]);
    }

    public function test_venta_pertenece_a_cliente(): void
    {
        $client = Client::factory()->create();
        $sale = Sale::factory()->create(['client_id' => $client->id]);

        $this->assertInstanceOf(Client::class, $sale->client);
        $this->assertEquals($client->id, $sale->client->id);
    }

    public function test_venta_pertenece_a_transporte(): void
    {
        $transport = Transport::factory()->create();
        $sale = Sale::factory()->create(['transport_id' => $transport->id]);

        $this->assertInstanceOf(Transport::class, $sale->transport);
        $this->assertEquals($transport->id, $sale->transport->id);
    }

    public function test_venta_pertenece_a_creador(): void
    {
        $user = User::factory()->create();
        $sale = Sale::factory()->create(['created_by' => $user->id]);

        $this->assertInstanceOf(User::class, $sale->creator);
        $this->assertEquals($user->id, $sale->creator->id);
    }

    public function test_venta_tiene_muchos_items(): void
    {
        $sale = Sale::factory()->create();
        SaleItem::factory()->count(3)->create(['sale_id' => $sale->id]);

        $this->assertCount(3, $sale->items);
        $this->assertInstanceOf(SaleItem::class, $sale->items->first());
    }

    public function test_cast_total_decimal(): void
    {
        $sale = Sale::factory()->create(['total' => 1500.75]);

        $this->assertEquals('1500.75', $sale->total);
    }
}
