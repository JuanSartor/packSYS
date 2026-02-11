<?php

namespace Database\Factories;

use App\Models\PaperCoil;
use Illuminate\Database\Eloquent\Factories\Factory;

class PaperCoilFactory extends Factory
{
    protected $model = PaperCoil::class;

    public function definition(): array
    {
        $pesoInicial = fake()->randomFloat(2, 100, 1000);

        return [
            'tipo_papel' => fake()->randomElement(['Kraft', 'Blanco', 'Reciclado', 'Satinado']),
            'ancho' => fake()->randomFloat(2, 10, 150),
            'gramaje' => fake()->randomFloat(2, 40, 200),
            'peso_inicial' => $pesoInicial,
            'peso_actual' => fake()->randomFloat(2, 10, $pesoInicial),
            'alerta_minima' => fake()->randomFloat(2, 5, 50),
            'eliminado' => false,
        ];
    }
}
