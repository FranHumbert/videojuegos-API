<?php

namespace Tests\Feature\Api;

use App\Models\User;
use App\Models\Plataforma;
use App\Models\Videojuego;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Passport\Passport;
use Tests\TestCase;

class PlataformaTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test: Listar todas las plataformas
     */
    public function test_authenticated_user_can_list_plataformas(): void
    {
        $user = User::factory()->create();
        Passport::actingAs($user);
        
        Plataforma::factory()->count(4)->create();

        $response = $this->getJson('/api/v1/plataformas');

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => [
                '*' => [
                    'id',
                    'nombre',
                    'fabricante',
                    'videojuegos_count'
                ]
            ]
        ]);
        $response->assertJsonCount(4, 'data');
    }

    /**
     * Test: Ver una plataforma específica
     */
    public function test_authenticated_user_can_view_specific_plataforma(): void
    {
        $user = User::factory()->create();
        Passport::actingAs($user);
        
        $plataforma = Plataforma::factory()->create([
            'nombre' => 'PlayStation 5'
        ]);

        $response = $this->getJson("/api/v1/plataformas/{$plataforma->id}");

        $response->assertStatus(200);
        $response->assertJson([
            'data' => [
                'id' => $plataforma->id,
                'nombre' => 'PlayStation 5'
            ]
        ]);
    }

    /**
     * Test: Obtener la plataforma más popular
     */
    public function test_can_get_most_popular_plataforma(): void
{
    $user = User::factory()->create();
    Passport::actingAs($user);
    
    $plataforma1 = Plataforma::factory()->create(['nombre' => 'PC']);
    $plataforma2 = Plataforma::factory()->create(['nombre' => 'PS5']);

    // Crear 5 videojuegos para PC (sin asociación automática)
    for ($i = 0; $i < 5; $i++) {
        $videojuego = Videojuego::factory()->create();
        $videojuego->plataformas()->sync([$plataforma1->id]);
    }

    // Crear 2 videojuegos para PS5
    for ($i = 0; $i < 2; $i++) {
        $videojuego = Videojuego::factory()->create();
        $videojuego->plataformas()->sync([$plataforma2->id]);
    }

    $response = $this->getJson('/api/v1/plataformas/mas-popular');

    $response->assertStatus(200);
    $response->assertJsonStructure([
        'data' => [
            'id',
            'nombre',
            'fabricante',
            'videojuegos_count',
            'videojuegos'
        ],
        'message'
    ]);
    
    // Verificar que es la plataforma con más videojuegos
    $this->assertEquals('PC', $response->json('data.nombre'));
    $this->assertEquals(5, $response->json('data.videojuegos_count'));
}

    /**
     * Test: Admin puede crear plataforma
     */
    public function test_admin_can_create_plataforma(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        Passport::actingAs($admin);

        $data = [
            'nombre' => 'Steam Deck',
            'fabricante' => 'Valve'
        ];

        $response = $this->postJson('/api/v1/plataformas', $data);

        $response->assertStatus(201);
        $response->assertJson([
            'message' => 'Plataforma creada exitosamente',
            'data' => [
                'nombre' => 'Steam Deck',
                'fabricante' => 'Valve'
            ]
        ]);

        $this->assertDatabaseHas('plataformas', [
            'nombre' => 'Steam Deck'
        ]);
    }

    /**
     * Test: Usuario normal NO puede crear plataforma
     */
    public function test_regular_user_cannot_create_plataforma(): void
    {
        $user = User::factory()->create(['role' => 'user']);
        Passport::actingAs($user);

        $data = [
            'nombre' => 'Steam Deck',
            'fabricante' => 'Valve'
        ];

        $response = $this->postJson('/api/v1/plataformas', $data);

        $response->assertStatus(403);
    }

    /**
     * Test: Crear plataforma requiere campo nombre
     */
    public function test_creating_plataforma_requires_nombre(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        Passport::actingAs($admin);

        $response = $this->postJson('/api/v1/plataformas', [
            'fabricante' => 'Sony'
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['nombre']);
    }

    /**
     * Test: Admin puede actualizar plataforma
     */
    public function test_admin_can_update_plataforma(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        Passport::actingAs($admin);
        
        $plataforma = Plataforma::factory()->create([
            'nombre' => 'PlayStation 5'
        ]);

        $response = $this->putJson("/api/v1/plataformas/{$plataforma->id}", [
            'nombre' => 'PlayStation 5 Pro'
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'message' => 'Plataforma actualizada exitosamente',
            'data' => [
                'nombre' => 'PlayStation 5 Pro'
            ]
        ]);

        $this->assertDatabaseHas('plataformas', [
            'id' => $plataforma->id,
            'nombre' => 'PlayStation 5 Pro'
        ]);
    }

    /**
     * Test: Admin puede eliminar plataforma
     */
    public function test_admin_can_delete_plataforma(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        Passport::actingAs($admin);
        
        $plataforma = Plataforma::factory()->create();

        $response = $this->deleteJson("/api/v1/plataformas/{$plataforma->id}");

        $response->assertStatus(200);
        $response->assertJson([
            'message' => 'Plataforma eliminada exitosamente'
        ]);

        $this->assertDatabaseMissing('plataformas', [
            'id' => $plataforma->id
        ]);
    }

    /**
     * Test: Usuario normal NO puede eliminar plataforma
     */
    public function test_regular_user_cannot_delete_plataforma(): void
    {
        $user = User::factory()->create(['role' => 'user']);
        Passport::actingAs($user);
        
        $plataforma = Plataforma::factory()->create();

        $response = $this->deleteJson("/api/v1/plataformas/{$plataforma->id}");

        $response->assertStatus(403);
    }

    /**
     * Test: Usuario no autenticado no puede acceder
     */
    public function test_unauthenticated_user_cannot_access_plataformas(): void
    {
        $response = $this->getJson('/api/v1/plataformas');

        $response->assertStatus(401);
    }
}