<?php

namespace Database\Factories;

use App\Models\Unidad;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class UnidadFactory extends Factory
{
    protected $model = Unidad::class;

    public function definition(): array
    {
        return [
            'descripcion' => fake()->randomElement(['Unidad', 'Kilogramo', 'Metro', 'Litro', 'Paquete', 'Caja']),
            'created_by' => User::factory(),
            'eliminado' => false,
        ];
    }
}
