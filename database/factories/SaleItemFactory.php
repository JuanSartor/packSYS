<?php

namespace Database\Factories;

use App\Models\SaleItem;
use App\Models\Sale;
use App\Models\Product;
use App\Models\ProductPrice;
use Illuminate\Database\Eloquent\Factories\Factory;

class SaleItemFactory extends Factory
{
    protected $model = SaleItem::class;

    public function definition(): array
    {
        return [
            'sale_id' => Sale::factory(),
            'product_id' => Product::factory(),
            'product_price_id' => ProductPrice::factory(),
            'cantidad' => fake()->numberBetween(1, 100),
            'precio_unitario_venta' => fake()->randomFloat(2, 10, 1000),
        ];
    }
}
