<?php

namespace Tests\Feature;

use App\Models\Client;
use App\Models\Facture;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FactureTest extends TestCase
{
    public function test_employe_ne_peut_pas_acceder_aux_factures(): void
    {
        $user = new User([
            'name' => 'Employe Simple',
            'email' => 'employe@test.sn',
            'role' => 'employe',
            'password' => bcrypt('password'),
        ]);

        $response = $this->actingAs($user)->get(route('factures.index'));

        // Redirection vers le dashboard pour accès non autorisé
        $response->assertRedirect(route('dashboard'));
    }

    public function test_calcul_automatique_numero_facture(): void
    {
        $numero = Facture::genererNumero();
        $this->assertStringStartsWith('FAC-', $numero);
        $this->assertMatchesRegularExpression('/^FAC-\d{6}-\d{4}$/', $numero);
    }
}
