<?php

namespace Database\Seeders;

use App\Models\Videojuego;
use Illuminate\Database\Seeder;

class VideojuegoSeeder extends Seeder
{
    public function run(): void
    {
        $videojuegos = [
            [
                'titulo' => 'The Legend of Zelda: Breath of the Wild',
                'anio_lanzamiento' => '2017-03-03',
                'genero' => 'Aventura',
                'plataformas' => [3] // Nintendo Switch
            ],
            [
                'titulo' => 'God of War Ragnarök',
                'anio_lanzamiento' => '2022-11-09',
                'genero' => 'Acción',
                'plataformas' => [1, 5] // PS5, PS4
            ],
            [
                'titulo' => 'Halo Infinite',
                'anio_lanzamiento' => '2021-12-08',
                'genero' => 'Shooter',
                'plataformas' => [2, 6, 4] // Xbox Series X, Xbox One, PC
            ],
            [
                'titulo' => 'Elden Ring',
                'anio_lanzamiento' => '2022-02-25',
                'genero' => 'RPG',
                'plataformas' => [1, 2, 4, 5, 6] // Multiplataforma
            ],
            [
                'titulo' => 'Spider-Man 2',
                'anio_lanzamiento' => '2023-10-20',
                'genero' => 'Acción',
                'plataformas' => [1] // PS5
            ],
        ];

        foreach ($videojuegos as $data) {
            $plataformas = $data['plataformas'];
            unset($data['plataformas']);
            
            $videojuego = Videojuego::create($data);
            $videojuego->plataformas()->attach($plataformas);
        }
    }
}