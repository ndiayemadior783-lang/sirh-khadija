<?php

namespace Database\Seeders;

use App\Models\Client;
use App\Models\Facture;
use App\Models\FactureLigne;
use App\Models\User;
use Illuminate\Database\Seeder;

class FacturationSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::where('role', 'admin')->first();

        // 1. Clients de test à Dakar
        $clients = [
            [
                'nom' => 'Sonatel SA',
                'contact_nom' => 'M. Cheikh Tidiane Sène (DRH)',
                'email' => 'drh@sonatel.sn',
                'telephone' => '+221 33 839 12 00',
                'adresse' => '64 Voie de Dégagement Nord (VDN)',
                'ville' => 'Dakar',
                'ninea' => '00124578 2K2',
                'rc_cm' => 'SN-DKR-1997-B-452',
                'notes' => 'Partenaire majeur - Missions annuelles d\'audit organisationnel et accompagnement RH.',
            ],
            [
                'nom' => 'Banque Atlantique Sénégal',
                'contact_nom' => 'Mme Aïssatou Ba (Responsable Formation)',
                'email' => 'aissatou.ba@banqueatlantique.sn',
                'telephone' => '+221 33 849 50 00',
                'adresse' => 'Avenue Roume, Plateau',
                'ville' => 'Dakar',
                'ninea' => '00348912 3L1',
                'rc_cm' => 'SN-DKR-2006-B-1120',
                'notes' => 'Contrat cadre pour formations managériales et gestion des talents.',
            ],
            [
                'nom' => 'TotalEnergies Marketing Sénégal',
                'contact_nom' => 'M. Amadou Fall (Directeur Administratif)',
                'email' => 'amadou.fall@totalenergies.sn',
                'telephone' => '+221 33 889 65 00',
                'adresse' => 'Route des Brasseries, Bel-Air',
                'ville' => 'Dakar',
                'ninea' => '00561234 4M3',
                'rc_cm' => 'SN-DKR-1975-B-105',
                'notes' => 'Missions ponctuelles de restructuration RH.',
            ],
        ];

        $clientModels = [];
        foreach ($clients as $cData) {
            $clientModels[] = Client::firstOrCreate(
                ['nom' => $cData['nom']],
                $cData
            );
        }

        // 2. Facture de démonstration (Payée)
        $facture1 = Facture::firstOrCreate(
            ['numero' => 'FAC-202610-0001'],
            [
                'client_id' => $clientModels[0]->id,
                'date_facture' => now()->subDays(15)->toDateString(),
                'date_echeance' => now()->addDays(15)->toDateString(),
                'statut' => 'payee',
                'mode_paiement' => 'virement',
                'date_paiement' => now()->subDays(2)->toDateString(),
                'reference_paiement' => 'VIR-SGBS-984521',
                'appliquer_tva' => true,
                'taux_tva' => 18.00,
                'total_ht' => 2500000,
                'montant_tva' => 450000,
                'total_ttc' => 2950000,
                'conditions_reglement' => 'Règlement par virement bancaire sous 30 jours à réception de facture.',
                'notes' => 'Mission d\'audit organisationnel et refonte des fiches de poste.',
                'created_by' => $admin?->id,
            ]
        );

        if ($facture1->lignes()->count() === 0) {
            FactureLigne::create([
                'facture_id' => $facture1->id,
                'designation' => 'Audit organisationnel et cartographie des compétences',
                'quantite' => 1,
                'prix_unitaire' => 1500000,
                'total_ligne' => 1500000,
            ]);
            FactureLigne::create([
                'facture_id' => $facture1->id,
                'designation' => 'Élaboration du référentiel des métiers & fiches de poste',
                'quantite' => 1,
                'prix_unitaire' => 1000000,
                'total_ligne' => 1000000,
            ]);
        }

        // 3. Facture de démonstration (Émise / En attente)
        $facture2 = Facture::firstOrCreate(
            ['numero' => 'FAC-202610-0002'],
            [
                'client_id' => $clientModels[1]->id,
                'date_facture' => now()->subDays(5)->toDateString(),
                'date_echeance' => now()->addDays(25)->toDateString(),
                'statut' => 'emise',
                'mode_paiement' => 'virement',
                'appliquer_tva' => true,
                'taux_tva' => 18.00,
                'total_ht' => 1200000,
                'montant_tva' => 216000,
                'total_ttc' => 1416000,
                'conditions_reglement' => 'Règlement par virement bancaire ou chèque sous 30 jours.',
                'notes' => 'Session de formation en leadership & management d\'équipe.',
                'created_by' => $admin?->id,
            ]
        );

        if ($facture2->lignes()->count() === 0) {
            FactureLigne::create([
                'facture_id' => $facture2->id,
                'designation' => 'Animation formation : « Leadership & Management transversal » (3 jours)',
                'quantite' => 3,
                'prix_unitaire' => 400000,
                'total_ligne' => 1200000,
            ]);
        }
    }
}
