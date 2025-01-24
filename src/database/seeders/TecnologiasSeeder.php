<?php

namespace Database\Seeders;

use App\Models\Tecnologia;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TecnologiasSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Crear 30.000  registros de tecnologías con nombre y descripción aleatorios
        Tecnologia::class::factory(30000)->create();
    }
}
