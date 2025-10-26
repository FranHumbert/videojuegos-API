<?php

namespace Tests\Feature\Api;

use App\Models\User;
use App\Models\Videojuego;
use App\Models\Plataforma;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Passport\Passport;
use Tests\TestCase;

class VideojuegoTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test: Listar todos los videojuegos (usuario autenticado)
     */
    public function test_authenticated_user_can_list_videojuegos(): void
    {
        $user = User::factory()->create();
        Passport::actingAs($user);
        
        Videojuego::factory()->count(3)->create();

        $response = $this->getJson('/api/v1/videojuegos');

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => [
                '*' => [
                    'id',
                    'titulo',
                    'anio_lanzamiento',
                    'genero',
                    'plataformas'
                ]
            ]
        ]);
        $response->assertJsonCount(3, 'data');
    }

    /**
     * Test: Usuario no autenticado no puede listar videojuegos
     */
    public function test_unauthenticated_user_cannot_list_videojuegos(): void
    {
        $response = $this->getJson('/api/v1/videojuegos');

        $response->assertStatus(401);
    }

    /**
     * Test: Ver un videojuego específico
     */
    public function test_authenticated_user_can_view_specific_videojuego(): void
    {
        $user = User::factory()->create();
        Passport::actingAs($user);
        
        $videojuego = Videojuego::factory()->create([
            'titulo' => 'Elden Ring'
        ]);

        $response = $this->getJson("/api/v1/videojuegos/{$videojuego->id}");

        $response->assertStatus(200);
        $response->assertJson([
            'data' => [
                'id' => $videojuego->id,
                'titulo' => 'Elden Ring'
            ]
        ]);
    }

    /**
     * Test: Ver videojuego que no existe devuelve 404
     */
    public function test_viewing_nonexistent_videojuego_returns_404(): void
    {
        $user = User::factory()->create();
        Passport::actingAs($user);

        $response = $this->getJson('/api/v1/videojuegos/999');

        $response->assertStatus(404);
    }

    /**
     * Test: Ver los 3 videojuegos más recientes
     */
    public function test_can_get_recent_videojuegos(): void
    {
        $user = User::factory()->create();
        Passport::actingAs($user);
        
        Videojuego::factory()->count(5)->create();

        $response = $this->getJson('/api/v1/videojuegos/recientes');

        $response->assertStatus(200);
        $response->assertJsonCount(3, 'data');
    }

    /**
     * Test: Admin puede crear videojuego
     */
    public function test_admin_can_create_videojuego(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        Passport::actingAs($admin);
        
        $plataforma = Plataforma::factory()->create();

        $data = [
            'titulo' => 'Nuevo Videojuego',
            'anio_lanzamiento' => '2024-01-15',
            'genero' => 'Acción',
            'plataformas' => [$plataforma->id]
        ];

        $response = $this->postJson('/api/v1/videojuegos', $data);

        $response->assertStatus(201);
        $response->assertJson([
            'message' => 'Videojuego creado exitosamente',
            'data' => [
                'titulo' => 'Nuevo Videojuego',
                'genero' => 'Acción'
            ]
        ]);

        $this->assertDatabaseHas('videojuegos', [
            'titulo' => 'Nuevo Videojuego'
        ]);
    }

    /**
     * Test: Usuario normal NO puede crear videojuego
     */
    public function test_regular_user_cannot_create_videojuego(): void
    {
        $user = User::factory()->create(['role' => 'user']);
        Passport::actingAs($user);

        $plataforma = Plataforma::factory()->create();

        $data = [
            'titulo' => 'Nuevo Videojuego',
            'anio_lanzamiento' => '2024-01-15',
            'genero' => 'Acción',
            'plataformas' => [$plataforma->id]
        ];

        $response = $this->postJson('/api/v1/videojuegos', $data);

        $response->assertStatus(403);
        $response->assertJson([
            'message' => 'No tienes permisos para realizar esta acción'
        ]);
    }

    /**
     * Test: Crear videojuego requiere campos obligatorios
     */
    public function test_creating_videojuego_requires_required_fields(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        Passport::actingAs($admin);

        $response = $this->postJson('/api/v1/videojuegos', []);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['titulo', 'anio_lanzamiento', 'genero', 'plataformas']);
    }

    /**
     * Test: Admin puede actualizar videojuego
     */
    public function test_admin_can_update_videojuego(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        Passport::actingAs($admin);
        
        $videojuego = Videojuego::factory()->create([
            'titulo' => 'Título Original'
        ]);

        $response = $this->putJson("/api/v1/videojuegos/{$videojuego->id}", [
            'titulo' => 'Título Actualizado'
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'message' => 'Videojuego actualizado exitosamente',
            'data' => [
                'titulo' => 'Título Actualizado'
            ]
        ]);

        $this->assertDatabaseHas('videojuegos', [
            'id' => $videojuego->id,
            'titulo' => 'Título Actualizado'
        ]);
    }

    /**
     * Test: Usuario normal NO puede actualizar videojuego
     */
    public function test_regular_user_cannot_update_videojuego(): void
    {
        $user = User::factory()->create(['role' => 'user']);
        Passport::actingAs($user);
        
        $videojuego = Videojuego::factory()->create();

        $response = $this->putJson("/api/v1/videojuegos/{$videojuego->id}", [
            'titulo' => 'Nuevo Título'
        ]);

        $response->assertStatus(403);
    }

    /**
     * Test: Admin puede eliminar videojuego
     */
    public function test_admin_can_delete_videojuego(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        Passport::actingAs($admin);
        
        $videojuego = Videojuego::factory()->create();

        $response = $this->deleteJson("/api/v1/videojuegos/{$videojuego->id}");

        $response->assertStatus(200);
        $response->assertJson([
            'message' => 'Videojuego eliminado exitosamente'
        ]);

        $this->assertDatabaseMissing('videojuegos', [
            'id' => $videojuego->id
        ]);
    }

    /**
     * Test: Usuario normal NO puede eliminar videojuego
     */
    public function test_regular_user_cannot_delete_videojuego(): void
    {
        $user = User::factory()->create(['role' => 'user']);
        Passport::actingAs($user);
        
        $videojuego = Videojuego::factory()->create();

        $response = $this->deleteJson("/api/v1/videojuegos/{$videojuego->id}");

        $response->assertStatus(403);
    }
}