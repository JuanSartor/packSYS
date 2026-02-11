<?php

namespace Database\Factories;

use App\Models\OrderStatus;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class OrderStatusFactory extends Factory
{
    protected $model = OrderStatus::class;

    public function definition(): array
    {
        return [
            'nombre' => fake()->randomElement(['Pendiente', 'En Proceso', 'Completado', 'Cancelado']),
            'descripcion' => fake()->sentence(),
            'created_by' => User::factory(),
            'eliminado' => false,
        ];
    }
}
