<?php

namespace Tests\Feature;

use App\Models\Admin;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ProfileTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function an_authenticated_administrator_can_create_a_profile()
    {
        // Créer ou authentifier un administrateur
    $admin = Admin::factory()->create();

    // Obtenir un token d'authentification
    $token = $admin->createToken('AdminToken')->plainTextToken;

    // Faire une requête POST pour créer un profil
    $response = $this->withHeader('Authorization', 'Bearer ' . $token)
        ->postJson('/api/profiles', [
            'name' => 'Test Profile',
            'description' => 'This is a test profile',
        ]);

    // Vérifier le statut de la réponse
    $response->assertStatus(201);
    }
}
