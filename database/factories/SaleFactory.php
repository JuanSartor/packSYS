<?php

namespace Database\Factories;

use App\Models\Sale;
use App\Models\Client;
use App\Models\Transport;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class SaleFactory extends Factory
{
    protected $model = Sale::class;

    public function definition(): array
    {
        return [
            'client_id' => Client::factory(),
            'transport_id' => Transport::factory(),
            'total' => fake()->randomFloat(2, 100, 10000),
            'created_by' => User::factory(),
            'eliminado' => false,
        ];
    }
}
