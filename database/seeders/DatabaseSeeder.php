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
     * departements et postes. Les employes, contrats, presences,
     * etc. sont ajoutes directement depuis l'interface.
     */
    public function run(): void
    {
        $password = Hash::make('password');

        // ---- Comptes de connexion ----
        User::firstOrCreate([
            'email' => 'admin@khadijaconsulting.sn',
        ], [
            'name' => 'Khadija Ndiaye',
            'password' => $password,
            'role' => 'admin',
            'phone' => '+221 77 123 45 67',
        ]);

        User::firstOrCreate([
            'email' => 'rh@khadijaconsulting.sn',
        ], [
            'name' => 'Aminata Diallo',
            'password' => $password,
            'role' => 'rh',
            'phone' => '+221 76 555 44 33',
        ]);

        User::firstOrCreate([
            'email' => 'manager@khadijaconsulting.sn',
        ], [
            'name' => 'Moussa Camara',
            'password' => $password,
            'role' => 'manager',
            'phone' => '+221 70 222 11 00',
        ]);

        // ---- Departements ----
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
            $depModel[] = Departement::firstOrCreate([
                'nom' => $dep['nom'],
            ], [
                'responsable' => $dep['responsable'],
            ]);
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
                Poste::firstOrCreate([
                    'departement_id' => $dep->id,
                    'libelle' => $libelle,
                ]);
            }
        }

        // ---- Facturation & Clients ----
        $this->call(FacturationSeeder::class);
    }
}