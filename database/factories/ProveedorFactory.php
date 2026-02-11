<?php

namespace Database\Factories;

use App\Models\Proveedor;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProveedorFactory extends Factory
{
    protected $model = Proveedor::class;

    public function definition(): array
    {
        return [
            'nombre' => fake()->company(),
            'descripcion' => fake()->sentence(),
            'created_by' => User::factory(),
            'eliminado' => false,
        ];
    }
}
