<?php

namespace Database\Factories;

use App\Models\ProductionTime;
use App\Models\ProductionOrder;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductionTimeFactory extends Factory
{
    protected $model = ProductionTime::class;

    public function definition(): array
    {
        $inicio = fake()->dateTimeBetween('-1 week', 'now');

        return [
            'production_order_id' => ProductionOrder::factory(),
            'inicio' => $inicio,
            'fin' => fake()->dateTimeBetween($inicio, 'now'),
        ];
    }

    public function enCurso(): static
    {
        return $this->state(fn (array $attributes) => [
            'inicio' => now(),
            'fin' => null,
        ]);
    }
}
