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
     * dÃ©partements et postes. Les employÃ©s, contrats, prÃ©sences,
     * etc. sont ajoutÃ©s directement depuis l'interface.
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

        // ---- DÃ©partements ----
        $departements = [
            ['nom' => 'Direction GÃ©nÃ©rale', 'responsable' => 'Khadija Ndiaye'],
            ['nom' => 'Ressources Humaines', 'responsable' => 'Aminata Diallo'],
            ['nom' => 'Consulting & StratÃ©gie', 'responsable' => 'Moussa Camara'],
            ['nom' => 'Finance & ComptabilitÃ©', 'responsable' => ''],
            ['nom' => 'Informatique & Digital', 'responsable' => ''],
            ['nom' => 'Marketing & Communication', 'responsable' => ''],
            ['nom' => 'Juridique & ConformitÃ©', 'responsable' => ''],
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
            'Direction GÃ©nÃ©rale' => ['Directeur GÃ©nÃ©ral', 'Assistante de Direction'],
            'Ressources Humaines' => ['Responsable RH', 'ChargÃ© de recrutement', 'Assistant RH'],
            'Consulting & StratÃ©gie' => ['Consultant Senior', 'Consultant ConfirmÃ©', 'Consultant Junior'],
            'Finance & ComptabilitÃ©' => ['Responsable Financier', 'Comptable', 'ContrÃ´leur de gestion'],
            'Informatique & Digital' => ['Responsable Informatique', 'DÃ©veloppeur Full-Stack', 'Data Analyst'],
            'Marketing & Communication' => ['Responsable Marketing', 'Community Manager'],
            'Juridique & ConformitÃ©' => ['Juriste'],
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
    }
}