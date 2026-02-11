<?php

namespace Database\Factories;

use App\Models\Client;
use App\Models\Canal;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ClientFactory extends Factory
{
    protected $model = Client::class;

    public function definition(): array
    {
        return [
            'nombre' => fake()->name(),
            'telefono' => fake()->phoneNumber(),
            'email' => fake()->unique()->safeEmail(),
            'direccion' => fake()->address(),
            'canal_id' => Canal::factory(),
            'created_by' => User::factory(),
            'eliminado' => false,
        ];
    }
}
