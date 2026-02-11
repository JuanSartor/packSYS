<?php

namespace Database\Factories;

use App\Models\ProductionOrder;
use App\Models\Product;
use App\Models\OrderStatus;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductionOrderFactory extends Factory
{
    protected $model = ProductionOrder::class;

    public function definition(): array
    {
        return [
            'product_id' => Product::factory(),
            'cantidad' => fake()->numberBetween(10, 1000),
            'estado' => fake()->randomElement(['espera', 'pendiente', 'produccion', 'pausada', 'finalizada']),
            'order_status_id' => OrderStatus::factory(),
            'created_by' => User::factory(),
            'started_at' => null,
            'finished_at' => null,
            'eliminado' => false,
        ];
    }

    public function enProceso(): static
    {
        return $this->state(fn (array $attributes) => [
            'estado' => 'produccion',
            'started_at' => now(),
            'finished_at' => null,
        ]);
    }

    public function completado(): static
    {
        return $this->state(fn (array $attributes) => [
            'estado' => 'finalizada',
            'started_at' => now()->subHours(2),
            'finished_at' => now(),
        ]);
    }
}
