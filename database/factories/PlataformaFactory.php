<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class PlataformaFactory extends Factory
{
    public function definition(): array
    {
        $plataformas = [
            ['nombre' => 'PlayStation', 'fabricante' => 'Sony'],
            ['nombre' => 'Xbox', 'fabricante' => 'Microsoft'],
            ['nombre' => 'Nintendo', 'fabricante' => 'Nintendo'],
            ['nombre' => 'PC', 'fabricante' => 'Varios'],
        ];

        $plataforma = fake()->randomElement($plataformas);

        return [
            'nombre' => $plataforma['nombre'] . ' ' . fake()->numberBetween(1, 5),
            'fabricante' => $plataforma['fabricante'],
        ];
    }
}