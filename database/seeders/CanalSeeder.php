<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Canal;

class CanalSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $canales = [
            ['descripcion' => 'Instagram'],
            ['descripcion' => 'Mercado Libre'],
            ['descripcion' => 'Personal'],
            ['descripcion' => 'Facebook'],
            ['descripcion' => 'WhatsApp'],
            ['descripcion' => 'Recomendación'],
            ['descripcion' => 'Otro'],
        ];

        foreach ($canales as $canal) {
            Canal::create($canal);
        }
    }
}
