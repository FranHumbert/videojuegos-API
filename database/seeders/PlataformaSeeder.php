<?php

namespace Database\Seeders;

use App\Models\Plataforma;
use Illuminate\Database\Seeder;

class PlataformaSeeder extends Seeder
{
    public function run(): void
    {
        $plataformas = [
            ['nombre' => 'PlayStation 5', 'fabricante' => 'Sony'],
            ['nombre' => 'Xbox Series X', 'fabricante' => 'Microsoft'],
            ['nombre' => 'Nintendo Switch', 'fabricante' => 'Nintendo'],
            ['nombre' => 'PC', 'fabricante' => 'Varios'],
            ['nombre' => 'PlayStation 4', 'fabricante' => 'Sony'],
            ['nombre' => 'Xbox One', 'fabricante' => 'Microsoft'],
        ];

        foreach ($plataformas as $plataforma) {
            Plataforma::create($plataforma);
        }
    }
}