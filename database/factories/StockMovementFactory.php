<?php

namespace Database\Factories;

use App\Models\StockMovement;
use App\Models\Product;
use App\Models\PaperCoil;
use Illuminate\Database\Eloquent\Factories\Factory;

class StockMovementFactory extends Factory
{
    protected $model = StockMovement::class;

    public function definition(): array
    {
        return [
            'product_id' => Product::factory(),
            'paper_coil_id' => null,
            'tipo' => fake()->randomElement(['entrada', 'salida', 'ajuste']),
            'cantidad' => fake()->randomFloat(2, 1, 100),
            'referencia' => fake()->randomElement(['venta', 'produccion', 'ajuste_manual']),
            'referencia_id' => fake()->numberBetween(1, 100),
        ];
    }

    public function paraBobina(): static
    {
        return $this->state(fn (array $attributes) => [
            'product_id' => null,
            'paper_coil_id' => PaperCoil::factory(),
        ]);
    }
}
