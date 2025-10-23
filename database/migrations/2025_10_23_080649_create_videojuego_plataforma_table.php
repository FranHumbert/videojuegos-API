<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('videojuego_plataforma', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_plataforma')->constrained('plataformas')->onDelete('cascade');
            $table->foreignId('id_videojuego')->constrained('videojuegos')->onDelete('cascade');
            $table->timestamps();

            $table->unique(['id_plataforma', 'id_videojuego']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('videojuego_plataforma');
    }
};
