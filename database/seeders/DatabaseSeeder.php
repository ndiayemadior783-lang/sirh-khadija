<?php

namespace Database\Seeders;

use App\Models\Departement;
use App\Models\Poste;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed la structure de base du SIRH : comptes de connexion,
     * départements et postes. Les employés, contrats, présences,
     * etc. sont ajoutés directement depuis l'interface.
     */
    public function run(): void
    {
        $password = Hash::make('password');

        // ---- Comptes de connexion ----
        User::factory()->create([
            'name' => 'Khadija Ndiaye',
            'email' => 'admin@khadijaconsulting.sn',
            'password' => $password,
            'role' => 'admin',
            'phone' => '+221 77 123 45 67',
        ]);

        User::factory()->create([
            'name' => 'Aminata Diallo',
            'email' => 'rh@khadijaconsulting.sn',
            'password' => $password,
            'role' => 'rh',
            'phone' => '+221 76 555 44 33',
        ]);

        User::factory()->create([
            'name' => 'Moussa Camara',
            'email' => 'manager@khadijaconsulting.sn',
            'password' => $password,
            'role' => 'manager',
            'phone' => '+221 70 222 11 00',
        ]);

        // ---- Départements ----
        $departements = [
            ['nom' => 'Direction Générale', 'responsable' => 'Khadija Ndiaye'],
            ['nom' => 'Ressources Humaines', 'responsable' => 'Aminata Diallo'],
            ['nom' => 'Consulting & Stratégie', 'responsable' => 'Moussa Camara'],
            ['nom' => 'Finance & Comptabilité', 'responsable' => ''],
            ['nom' => 'Informatique & Digital', 'responsable' => ''],
            ['nom' => 'Marketing & Communication', 'responsable' => ''],
            ['nom' => 'Juridique & Conformité', 'responsable' => ''],
        ];

        $depModel = [];
        foreach ($departements as $dep) {
            $depModel[] = Departement::create($dep);
        }

        // ---- Postes ----
        $postesData = [
            'Direction Générale' => ['Directeur Général', 'Assistante de Direction'],
            'Ressources Humaines' => ['Responsable RH', 'Chargé de recrutement', 'Assistant RH'],
            'Consulting & Stratégie' => ['Consultant Senior', 'Consultant Confirmé', 'Consultant Junior'],
            'Finance & Comptabilité' => ['Responsable Financier', 'Comptable', 'Contrôleur de gestion'],
            'Informatique & Digital' => ['Responsable Informatique', 'Développeur Full-Stack', 'Data Analyst'],
            'Marketing & Communication' => ['Responsable Marketing', 'Community Manager'],
            'Juridique & Conformité' => ['Juriste'],
        ];

        foreach ($postesData as $depNom => $postes) {
            $dep = $depModel[array_search($depNom, array_column($departements, 'nom'))];
            foreach ($postes as $libelle) {
                Poste::create([
                    'departement_id' => $dep->id,
                    'libelle' => $libelle,
                ]);
            }
        }
    }
}