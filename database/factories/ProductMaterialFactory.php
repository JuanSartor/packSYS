<?php

namespace Database\Factories;

use App\Models\ProductMaterial;
use App\Models\Product;
use App\Models\PaperCoil;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductMaterialFactory extends Factory
{
    protected $model = ProductMaterial::class;

    public function definition(): array
    {
        return [
            'product_id' => Product::factory(),
            'paper_coil_id' => PaperCoil::factory(),
            'consumo_por_unidad' => fake()->randomFloat(4, 0.001, 1),
        ];
    }
}
