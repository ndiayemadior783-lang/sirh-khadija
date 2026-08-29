<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    use RefreshDatabase;

    public function test_la_page_de_connexion_est_accessible(): void
    {
        $response = $this->get('/login');

        $response->assertStatus(200);
    }

    public function test_le_tableau_de_bord_exige_une_authentification(): void
    {
        $response = $this->get('/dashboard');

        $response->assertRedirect('/login');
    }

    public function test_le_dashboard_est_accessible_apres_connexion(): void
    {
        $user = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($user)->get('/dashboard');

        $response->assertStatus(200);
    }
}