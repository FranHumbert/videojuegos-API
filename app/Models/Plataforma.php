<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Plataforma extends Model
{
    use HasFactory;

    protected $fillable = [
        'nombre',
        'fabricante',
    ];

    public function videojuegos(): BelongsToMany
    {
        return $this->belongsToMany(
            Videojuego::class,
            'videojuego_plataforma',
            'id_plataforma',
            'id_videojuego'
        )->withTimestamps();
    }
}