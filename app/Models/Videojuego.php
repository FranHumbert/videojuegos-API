<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Videojuego extends Model
{
    use HasFactory;

    protected $fillable = [
        'titulo',
        'anio_lanzamiento',
        'genero',
    ];

    protected function casts(): array
    {
        return [
            'anio_lanzamiento' => 'date',
        ];
    }

    public function plataformas(): BelongsToMany
    {
        return $this->belongsToMany(
            Plataforma::class,
            'videojuego_plataforma',
            'id_videojuego',
            'id_plataforma'
        )->withTimestamps();
    }
}