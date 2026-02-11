<?php

namespace Database\Factories;

use App\Models\Transport;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class TransportFactory extends Factory
{
    protected $model = Transport::class;

    public function definition(): array
    {
        return [
            'nombre' => fake()->company() . ' Transporte',
            'costo' => fake()->randomFloat(2, 100, 5000),
            'created_by' => User::factory(),
            'eliminado' => false,
        ];
    }
}
