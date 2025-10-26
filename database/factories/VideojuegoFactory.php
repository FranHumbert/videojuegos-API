<?php

namespace Database\Factories;

use App\Models\Plataforma;
use App\Models\Videojuego;
use Illuminate\Database\Eloquent\Factories\Factory;

class VideojuegoFactory extends Factory
{
    public function definition(): array
    {
        $generos = ['Acción', 'Aventura', 'RPG', 'Shooter', 'Estrategia', 'Deportes'];

        return [
            'titulo' => fake()->sentence(3),
            'anio_lanzamiento' => fake()->dateTimeBetween('-10 years', 'now'),
            'genero' => fake()->randomElement($generos),
        ];
    }

    public function configure(): static
    {
        return $this->afterCreating(function (Videojuego $videojuego) {
            if (Plataforma::count() > 0) {
                $plataformas = Plataforma::inRandomOrder()->limit(rand(1, 3))->pluck('id');
                $videojuego->plataformas()->attach($plataformas);
            }
        });
    }
}