<?php

namespace Database\Factories;

use App\Models\ProductPrice;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductPriceFactory extends Factory
{
    protected $model = ProductPrice::class;

    public function definition(): array
    {
        $costo = fake()->randomFloat(2, 10, 500);

        return [
            'product_id' => Product::factory(),
            'costo' => $costo,
            'precio_venta' => $costo * fake()->randomFloat(2, 1.2, 2.5),
            'vigente_desde' => fake()->dateTimeBetween('-1 year', 'now'),
            'created_by' => User::factory(),
            'eliminado' => false,
        ];
    }
}
