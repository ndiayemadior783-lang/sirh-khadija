<?php

use App\Http\Controllers\BulletinPaieController;
use App\Http\Controllers\CongeController;
use App\Http\Controllers\ContratController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DepartementController;
use App\Http\Controllers\EmployeController;
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
    Route::get('bulletins/{bulletin}', [BulletinPaieController::class, 'show'])->middleware('role:admin,rh')->name('bulletins.show');

    // Gestion du profil utilisateur
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});