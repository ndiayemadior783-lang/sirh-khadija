<?php

use App\Http\Controllers\BulletinPaieController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\CongeController;
use App\Http\Controllers\ContratController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DepartementController;
use App\Http\Controllers\EmployeController;
use App\Http\Controllers\FactureController;
use App\Http\Controllers\FormationController;
use App\Http\Controllers\PresenceController;
use App\Http\Controllers\PrimeController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('dashboard');
});

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Conges : tous les utilisateurs peuvent consulter et soumettre
    Route::get('conges', [CongeController::class, 'index'])->name('conges.index');
    Route::get('conges/create', [CongeController::class, 'create'])->name('conges.create');
    Route::post('conges', [CongeController::class, 'store'])->name('conges.store');

    // Validation des conges : gestionnaires
    Route::middleware('role:admin,rh,manager')->group(function () {
        Route::post('conges/{conge}/valider', [CongeController::class, 'valider'])->name('conges.valider');
        Route::post('conges/{conge}/rejeter', [CongeController::class, 'rejeter'])->name('conges.rejeter');
    });

    // Planning des conges : gestionnaires
    Route::middleware('role:admin,rh,manager')->group(function () {
        Route::get('conges/planning', [CongeController::class, 'planning'])->name('conges.planning');
        Route::get('conges/planning/pdf', [CongeController::class, 'planningPdf'])->name('conges.planning-pdf');
    });

    // Employes : consultation pour les gestionnaires, ecriture admin/RH
    Route::get('employes', [EmployeController::class, 'index'])->middleware('role:admin,rh,manager')->name('employes.index');
    Route::get('employes/create', [EmployeController::class, 'create'])->middleware('role:admin,rh')->name('employes.create');
    Route::post('employes', [EmployeController::class, 'store'])->middleware('role:admin,rh')->name('employes.store');
    Route::get('employes/{employe}', [EmployeController::class, 'show'])->middleware('role:admin,rh,manager')->name('employes.show');
    Route::get('employes/{employe}/edit', [EmployeController::class, 'edit'])->middleware('role:admin,rh')->name('employes.edit');
    Route::put('employes/{employe}', [EmployeController::class, 'update'])->middleware('role:admin,rh')->name('employes.update');
    Route::delete('employes/{employe}', [EmployeController::class, 'destroy'])->middleware('role:admin,rh')->name('employes.destroy');

    // Formations : consultation pour les gestionnaires, ecriture admin/RH
    Route::get('formations', [FormationController::class, 'index'])->middleware('role:admin,rh,manager')->name('formations.index');
    Route::get('formations/create', [FormationController::class, 'create'])->middleware('role:admin,rh')->name('formations.create');
    Route::post('formations', [FormationController::class, 'store'])->middleware('role:admin,rh')->name('formations.store');
    Route::get('formations/{formation}', [FormationController::class, 'show'])->middleware('role:admin,rh,manager')->name('formations.show');
    Route::post('formations/{formation}/inscrire', [FormationController::class, 'inscrire'])->middleware('role:admin,rh')->name('formations.inscrire');

    // Contrats : admin/RH
    Route::get('contrats', [ContratController::class, 'index'])->middleware('role:admin,rh')->name('contrats.index');
    Route::get('contrats/create', [ContratController::class, 'create'])->middleware('role:admin,rh')->name('contrats.create');
    Route::post('contrats', [ContratController::class, 'store'])->middleware('role:admin,rh')->name('contrats.store');
    Route::get('contrats/{contrat}', [ContratController::class, 'show'])->middleware('role:admin,rh')->name('contrats.show');
    Route::delete('contrats/{contrat}', [ContratController::class, 'destroy'])->middleware('role:admin,rh')->name('contrats.destroy');

    // Departements : admin/RH
    Route::get('departements', [DepartementController::class, 'index'])->middleware('role:admin,rh')->name('departements.index');
    Route::get('departements/create', [DepartementController::class, 'create'])->middleware('role:admin,rh')->name('departements.create');
    Route::post('departements', [DepartementController::class, 'store'])->middleware('role:admin,rh')->name('departements.store');
    Route::delete('departements/{departement}', [DepartementController::class, 'destroy'])->middleware('role:admin,rh')->name('departements.destroy');

    // Presences : consultation gestionnaires, ecriture admin/RH
    Route::get('presences', [PresenceController::class, 'index'])->middleware('role:admin,rh,manager')->name('presences.index');
    Route::get('presences/create', [PresenceController::class, 'create'])->middleware('role:admin,rh')->name('presences.create');
    Route::post('presences', [PresenceController::class, 'store'])->middleware('role:admin,rh')->name('presences.store');
    Route::delete('presences/{presence}', [PresenceController::class, 'destroy'])->middleware('role:admin,rh')->name('presences.destroy');

    // Primes : admin/RH
    Route::get('primes', [PrimeController::class, 'index'])->middleware('role:admin,rh')->name('primes.index');
    Route::get('primes/create', [PrimeController::class, 'create'])->middleware('role:admin,rh')->name('primes.create');
    Route::post('primes', [PrimeController::class, 'store'])->middleware('role:admin,rh')->name('primes.store');
    Route::delete('primes/{prime}', [PrimeController::class, 'destroy'])->middleware('role:admin,rh')->name('primes.destroy');

    // Bulletins de paie : admin/RH
    Route::get('bulletins', [BulletinPaieController::class, 'index'])->middleware('role:admin,rh')->name('bulletins.index');
    Route::post('bulletins/generer', [BulletinPaieController::class, 'generer'])->middleware('role:admin,rh')->name('bulletins.generer');
    Route::post('bulletins/envoyer', [BulletinPaieController::class, 'envoyerTous'])->middleware(['role:admin,rh', 'throttle:5,1'])->name('bulletins.envoyer-tous');
    Route::get('bulletins/{bulletin}', [BulletinPaieController::class, 'show'])->middleware('role:admin,rh')->name('bulletins.show');
    Route::post('bulletins/{bulletin}/envoyer', [BulletinPaieController::class, 'envoyer'])->middleware(['role:admin,rh', 'throttle:20,1'])->name('bulletins.envoyer');


    // Journal d activite : admin
    Route::middleware('role:admin')->group(function () {
        Route::get('journal', [App\Http\Controllers\JournalController::class, 'index'])->name('journal.index');
        Route::post('journal/clear', [App\Http\Controllers\JournalController::class, 'clear'])->name('journal.clear');
    });
    // Livre de paie : admin/RH
    Route::get('livre-paie', [App\Http\Controllers\LivrePaieController::class, 'livrePaie'])->middleware('role:admin,rh')->name('livre-paie.index');

    // Etat des cotisations : admin/RH
    Route::get('cotisations', [App\Http\Controllers\LivrePaieController::class, 'etatCotisations'])->middleware('role:admin,rh')->name('cotisations.index');

    // Facturation & Clients : admin/RH
    Route::middleware('role:admin,rh')->group(function () {
        Route::resource('clients', ClientController::class);
        Route::resource('factures', FactureController::class);
        Route::post('factures/{facture}/marquer-payee', [FactureController::class, 'marquerPayee'])->name('factures.marquer-payee');
        Route::post('factures/{facture}/changer-statut', [FactureController::class, 'changerStatut'])->name('factures.changer-statut');
        Route::get('factures/{facture}/pdf', [FactureController::class, 'pdf'])->name('factures.pdf');
    });


    
    // Export CSV/Excel
    Route::get('export/employes/csv', [App\Http\Controllers\ExportController::class, 'employesCsv'])->middleware('role:admin,rh,manager')->name('export.employes-csv');
    Route::get('export/bulletins/csv', [App\Http\Controllers\ExportController::class, 'bulletinsCsv'])->middleware('role:admin,rh')->name('export.bulletins-csv');
    Route::get('export/conges/csv', [App\Http\Controllers\ExportController::class, 'congesCsv'])->middleware('role:admin,rh')->name('export.conges-csv');
    Route::get('export/presences/csv', [App\Http\Controllers\ExportController::class, 'presencesCsv'])->middleware('role:admin,rh')->name('export.presences-csv');

        // Liste des attestations (page globale)
    Route::get('attestations', [App\Http\Controllers\ExportController::class, 'attestationsIndex'])->middleware('role:admin,rh')->name('attestations.index');

    Route::get('export/attestations/pdf', [App\Http\Controllers\ExportController::class, 'listeAttestations'])->middleware('role:admin,rh')->name('export.attestations-pdf');
    // Registre unique du personnel (RUP)
    Route::get('registre', [App\Http\Controllers\RegistreController::class, 'index'])->middleware('role:admin,rh')->name('registre.index');
    Route::get('registre/pdf', [App\Http\Controllers\RegistreController::class, 'pdf'])->middleware('role:admin,rh')->name('registre.pdf');
    Route::get('registre/csv', [App\Http\Controllers\RegistreController::class, 'csv'])->middleware('role:admin,rh')->name('registre.csv');
    // Attestations
    Route::get('export/{employe}/attestation-travail.pdf', [App\Http\Controllers\ExportController::class, 'attestationTravail'])->middleware('role:admin,rh')->name('export.attestation-travail');
    Route::get('export/{employe}/attestation-salaire.pdf', [App\Http\Controllers\ExportController::class, 'attestationSalaire'])->middleware('role:admin,rh')->name('export.attestation-salaire');
    Route::get('export/{employe}/certificat-travail.pdf', [App\Http\Controllers\ExportController::class, 'certificatTravail'])->middleware('role:admin,rh')->name('export.certificat-travail');

    // Export PDF
    Route::get('export/bulletins/{bulletin}/pdf', [App\Http\Controllers\ExportController::class, 'bulletin'])->middleware('role:admin,rh')->name('export.bulletin');
    Route::get('export/livre-paie/pdf', [App\Http\Controllers\ExportController::class, 'livrePaie'])->middleware('role:admin,rh')->name('export.livre-paie');
    Route::get('export/cotisations/pdf', [App\Http\Controllers\ExportController::class, 'etatCotisations'])->middleware('role:admin,rh')->name('export.cotisations');
    Route::get('export/employes/pdf', [App\Http\Controllers\ExportController::class, 'listeEmployes'])->middleware('role:admin,rh,manager')->name('export.employes');

    
        // Utilisateurs
    Route::get('utilisateurs', [App\Http\Controllers\UserController::class, 'index'])->middleware('role:admin')->name('utilisateurs.index');
    Route::get('utilisateurs/create', [App\Http\Controllers\UserController::class, 'create'])->middleware('role:admin')->name('utilisateurs.create');
    Route::post('utilisateurs', [App\Http\Controllers\UserController::class, 'store'])->middleware('role:admin')->name('utilisateurs.store');
    Route::get('utilisateurs/{user}/edit', [App\Http\Controllers\UserController::class, 'edit'])->middleware('role:admin')->name('utilisateurs.edit');
    Route::put('utilisateurs/{user}', [App\Http\Controllers\UserController::class, 'update'])->middleware('role:admin')->name('utilisateurs.update');
    Route::post('utilisateurs/{user}/reset-password', [App\Http\Controllers\UserController::class, 'resetPassword'])->middleware('role:admin')->name('utilisateurs.reset-password');
    Route::delete('utilisateurs/{user}', [App\Http\Controllers\UserController::class, 'destroy'])->middleware('role:admin')->name('utilisateurs.destroy');

    // Parametres
    Route::get('parametres', [App\Http\Controllers\ParametreController::class, 'index'])->middleware('role:admin,rh')->name('parametres.index');
    Route::post('parametres', [App\Http\Controllers\ParametreController::class, 'update'])->middleware('role:admin,rh')->name('parametres.update');
    Route::post('parametres/test-smtp', [App\Http\Controllers\ParametreController::class, 'testSmtp'])->middleware(['role:admin,rh', 'throttle:5,1'])->name('parametres.test-smtp');
    Route::post('parametres/relancer-emails', [App\Http\Controllers\ParametreController::class, 'relancerEchecs'])->middleware('role:admin,rh')->name('parametres.relancer-emails');

    // Gestion du profil utilisateur
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});
