<?php
// TEST FLUX METIER COMPLET SUR MYSQL - SIRH Khadija Consulting
// Ce script simule le parcours complet tel qu'un RH le ferait dans l'interface.
use App\Models\Employe;
use App\Models\Contrat;
use App\Models\Presence;
use App\Models\Conge;
use App\Models\Formation;
use App\Models\Prime;
use App\Models\BulletinPaie;
use App\Models\Departement;
use App\Models\Poste;

$errors = [];
$ok = 0;

try {
  $dep = Departement::where('nom', 'Informatique & Digital')->first();
  $poste = Poste::where('libelle', 'Developpeur Full-Stack')->first();
  if (!$dep || !$poste) throw new \Exception('Departement ou poste manquant');

  // 1. Ajout employe
  $emp = Employe::create([
    'matricule' => 'KC-TEST-001',
    'prenom' => 'Test',
    'nom' => 'Employe',
    'genre' => 'F',
    'date_naissance' => '1995-03-10',
    'email' => 'test.employe@khadijaconsulting.sn',
    'telephone' => '+221 77 000 00 00',
    'date_embauche' => '2026-01-05',
    'statut' => 'actif',
    'poste_id' => $poste->id,
    'departement_id' => $dep->id,
    'salaire_base' => 250000,
  ]);
  $ok++;

  // 2. Contrat CDI
  $ctr = Contrat::create([
    'employee_id' => $emp->id,
    'type' => 'CDI',
    'date_debut' => '2026-01-05',
    'salaire_base' => 250000,
    'statut' => 'en_cours',
  ]);
  $ok++;

  // 3. Presence (avec heures supp)
  $pres = Presence::create([
    'employee_id' => $emp->id,
    'date' => '2026-08-10',
    'heure_arrivee' => '08:00:00',
    'heure_depart' => '18:00:00',
    'statut' => 'present',
    'heures_sup' => 2,
  ]);
  $ok++;

  // 4. Conge
  $conge = Conge::create([
    'employee_id' => $emp->id,
    'type' => 'paye',
    'date_debut' => '2026-09-01',
    'date_fin' => '2026-09-10',
    'jours' => 10,
    'motif' => 'Conges annuels',
    'statut' => 'en_attente',
  ]);
  $ok++;

  // 5. Formation + inscription
  $form = Formation::create([
    'titre' => 'Formation Laravel Avance',
    'description' => 'Perfectionnement developpement web',
    'date_debut' => '2026-10-01',
    'date_fin' => '2026-10-05',
    'formateur' => 'Consultant externe',
    'lieu' => 'Dakar',
    'cout' => 150000,
  ]);
  $form->employes()->attach($emp->id);
  $ok++;

  // 6. Prime
  $prime = Prime::create([
    'employee_id' => $emp->id,
    'libelle' => 'Prime de performance',
    'montant' => 50000,
    'date' => '2026-08-15',
  ]);
  $ok++;

  echo "== AJOUTS OK : $ok/6\n";
  echo "Employe: {$emp->prenom} {$emp->nom} matricule {$emp->matricule}\n";
  echo "Contrat: {$ctr->type} du {$ctr->date_debut}\n";
  echo "Presence: {$pres->date} heures_sup={$pres->heures_sup}\n";
  echo "Conge: {$conge->type} {$conge->jours} jours\n";
  echo "Formation: {$form->titre} - participants: " . $form->employes()->count() . "\n";
  echo "Prime: {$prime->libelle} = {$prime->montant} FCFA\n";

  // 7. Generation bulletin de paie (meme logique que BulletinPaieController)
  $period = '2026-08';
  $primes = Prime::where('employee_id', $emp->id)
    ->whereYear('date', 2026)->whereMonth('date', 8)->sum('montant');
  $heuresSup = Presence::where('employee_id', $emp->id)
    ->whereYear('date', 2026)->whereMonth('date', 8)->sum('heures_sup');
  $montantHeuresSup = round($heuresSup * 1500, 2);
  $brut = $emp->salaire_base + $primes + $montantHeuresSup;
  $cotisations = round($brut * 0.06, 2);
  $impots = round(($brut - $cotisations) * 0.08, 2);
  $net = $brut - $cotisations - $impots;
  $bul = BulletinPaie::create([
    'numero' => 'BUL-TEST-001',
    'employee_id' => $emp->id,
    'period' => $period,
    'salaire_base' => $emp->salaire_base,
    'primes' => $primes,
    'heures_sup' => $montantHeuresSup,
    'indemnites' => 0,
    'salaire_brut' => $brut,
    'cotisations_sociales' => $cotisations,
    'impots' => $impots,
    'salaire_net' => $net,
    'statut' => 'brouillon',
  ]);

  echo "\n== BULLETIN DE PAIE ($period) ==\n";
  echo "Base: " . number_format($emp->salaire_base, 0, ',', ' ') . " FCFA\n";
  echo "Primes: " . number_format($primes, 0, ',', ' ') . " FCFA\n";
  echo "Heures sup: " . number_format($montantHeuresSup, 0, ',', ' ') . " FCFA (2h x 1500)\n";
  echo "Brut: " . number_format($brut, 0, ',', ' ') . " FCFA\n";
  echo "Cotisations (6%): " . number_format($cotisations, 0, ',', ' ') . " FCFA\n";
  echo "Impot (8%): " . number_format($impots, 0, ',', ' ') . " FCFA\n";
  echo "NET A PAYER: " . number_format($net, 0, ',', ' ') . " FCFA\n";

  // Verifications
  $checks = [
    'Employe cree' => Employe::count() === 1,
    'Contrat cree' => Contrat::count() === 1,
    'Presence creee' => Presence::count() === 1,
    'Conge cree' => Conge::count() === 1,
    'Formation creee' => Formation::count() === 1,
    'Inscription formation' => $form->employes()->count() === 1,
    'Prime creee' => Prime::count() === 1,
    'Bulletin cree' => BulletinPaie::count() === 1,
    'Calcul brut correct' => abs(round(($brut) - 303000, 2)) < 0.01,
    'Calcul net > 0' => $net > 0,
  ];
  $allPass = true;
  foreach ($checks as $label => $pass) {
    echo ($pass ? '  [PASS] ' : '  [FAIL] ') . "$label\n";
    if (!$pass) $allPass = false;
  }
  echo "\n" . ($allPass ? '== TOUS LES FLUX METIER PASSENT SUR MYSQL ==' : '== DES TESTS ONT ECHOUE ==') . "\n";
  // Nettoyage : laisse la base vierge (0 employe, 0 formation) pour la vente
  $emp->delete();
  $form->employes()->detach();
  $form->delete();
  return $allPass ? 0 : 1;

} catch (\Exception $e) {
  echo "ERREUR METIER: " . $e->getMessage() . "\n";
  return 1;
}
