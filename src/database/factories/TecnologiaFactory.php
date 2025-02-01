<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Tecnologia>
 */
class TecnologiaFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'nombre'        => $this->faker->sentence(2),
            'descripcion'   => $this->faker->paragraph(1),
            'estado'        => $this->faker->randomElement(['ACTIVO', 'INACTIVO']),
            'pdf'           => null
        ];
    }
}
