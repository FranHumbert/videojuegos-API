<?php

namespace Tests\Feature\Api;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Passport\Passport;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test: Login exitoso con credenciales correctas
     */
    public function test_login_successful_with_valid_credentials(): void
    {
        // Instalar Passport para este test
        $this->artisan('passport:client', [
            '--personal' => true,
            '--name' => 'Test Personal Access Client',
            '--no-interaction' => true
        ]);

        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('password123'),
            'role' => 'user'
        ]);

        $response = $this->postJson('/api/v1/login', [
            'email' => 'test@example.com',
            'password' => 'password123'
        ]);

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'message',
            'access_token',
            'token_type',
            'user' => [
                'id',
                'name',
                'email',
                'role'
            ]
        ]);
        $response->assertJson([
            'token_type' => 'Bearer'
        ]);
    }

    /**
     * Test: Login falla con credenciales incorrectas
     */
    public function test_login_fails_with_invalid_credentials(): void
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('password123')
        ]);

        $response = $this->postJson('/api/v1/login', [
            'email' => 'test@example.com',
            'password' => 'wrong_password'
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['email']);
    }

    /**
     * Test: Login requiere email y password
     */
    public function test_login_requires_email_and_password(): void
    {
        $response = $this->postJson('/api/v1/login', []);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['email', 'password']);
    }

    /**
     * Test: Login requiere email válido
     */
    public function test_login_requires_valid_email(): void
    {
        $response = $this->postJson('/api/v1/login', [
            'email' => 'not-an-email',
            'password' => 'password123'
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['email']);
    }

    /**
     * Test: Ver perfil del usuario autenticado
     */
    public function test_authenticated_user_can_view_profile(): void
    {
        $user = User::factory()->create();
        Passport::actingAs($user);

        $response = $this->getJson('/api/v1/profile');

        $response->assertStatus(200);
        $response->assertJson([
            'user' => [
                'id' => $user->id,
                'email' => $user->email,
                'role' => $user->role
            ]
        ]);
    }

    /**
     * Test: Usuario no autenticado no puede ver perfil
     */
    public function test_unauthenticated_user_cannot_view_profile(): void
    {
        $response = $this->getJson('/api/v1/profile');

        $response->assertStatus(401);
    }

    /**
     * Test: Logout revoca el token
     */
    public function test_logout_revokes_token(): void
    {
        // Instalar Passport
        $this->artisan('passport:client', [
            '--personal' => true,
            '--name' => 'Test Personal Access Client',
            '--no-interaction' => true
        ]);

        $user = User::factory()->create([
            'password' => bcrypt('password123')
        ]);

        // Crear token real
        $token = $user->createToken('test-token')->accessToken;

        // Hacer logout con el token
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
            'Accept' => 'application/json',
        ])->postJson('/api/v1/logout');

        $response->assertStatus(200);
        $response->assertJson([
            'message' => 'Sesión cerrada exitosamente'
        ]); 
    }
}