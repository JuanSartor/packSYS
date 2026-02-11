<?php

namespace Database\Factories;

use App\Models\Canal;
use Illuminate\Database\Eloquent\Factories\Factory;

class CanalFactory extends Factory
{
    protected $model = Canal::class;

    public function definition(): array
    {
        return [
            'descripcion' => fake()->randomElement(['Facebook', 'Instagram', 'Referido', 'Página Web', 'WhatsApp']),
            'eliminado' => false,
        ];
    }
}
