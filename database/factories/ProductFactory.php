<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\ProductType;
use App\Models\Proveedor;
use App\Models\Unidad;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductFactory extends Factory
{
    protected $model = Product::class;

    public function definition(): array
    {
        $usaBobina = fake()->boolean();

        return [
            'name' => fake()->words(3, true),
            'descripcion' => fake()->sentence(),
            'product_type_id' => ProductType::factory(),
            'proveedor_id' => Proveedor::factory(),
            'unidad_id' => Unidad::factory(),
            'stock_actual' => fake()->randomFloat(2, 0, 1000),
            'stock_minimo' => fake()->randomFloat(2, 5, 50),
            'usa_bobina' => $usaBobina,
            'ancho' => $usaBobina ? fake()->randomFloat(2, 10, 100) : null,
            'largo' => $usaBobina ? fake()->randomFloat(2, 10, 100) : null,
            'fuelle' => $usaBobina ? fake()->randomFloat(2, 0, 20) : null,
            'created_by' => User::factory(),
            'eliminado' => false,
        ];
    }

    public function conBobina(): static
    {
        return $this->state(fn (array $attributes) => [
            'usa_bobina' => true,
            'ancho' => fake()->randomFloat(2, 10, 100),
            'largo' => fake()->randomFloat(2, 10, 100),
            'fuelle' => fake()->randomFloat(2, 0, 20),
        ]);
    }

    public function sinBobina(): static
    {
        return $this->state(fn (array $attributes) => [
            'usa_bobina' => false,
            'ancho' => null,
            'largo' => null,
            'fuelle' => null,
        ]);
    }
}
