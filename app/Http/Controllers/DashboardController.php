<?php

namespace App\Http\Controllers;

use App\Models\BulletinPaie;
use App\Models\Conge;
use App\Models\Departement;
use App\Models\Employe;
use App\Models\Presence;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        if ($user->role === 'employe') {
            return $this->employeDashboard($user);
        }

        $totalEmployes = Employe::count();
        $actifs = Employe::where('statut', 'actif')->count();
        $departements = Departement::count();
        $congesEnAttente = Conge::where('statut', 'en_attente')->count();

        // Répartition par département
        $parDepartement = Employe::selectRaw('departements.nom, count(*) as total')
            ->join('departements', 'employees.departement_id', '=', 'departements.id')
            ->groupBy('departements.nom')
            ->orderByDesc('total')
            ->pluck('total', 'nom');

        // Masse salariale
        $masseSalariale = Employe::where('statut', 'actif')->sum('salaire_base');
        $bulletinCourant = BulletinPaie::where('period', now()->format('Y-m'))->sum('salaire_net');

        // Présences du jour
        $aujourdhui = now()->toDateString();
        $presencesJour = Presence::where('date', $aujourdhui)->count();
        $presentJour = Presence::where('date', $aujourdhui)->where('statut', 'present')->count();
        $absentJour = Presence::where('date', $aujourdhui)->where('statut', 'absent')->count();

        // Statuts des employés
        $statuts = Employe::selectRaw('statut, count(*) as total')->groupBy('statut')->pluck('total', 'statut');

        // Derniers employés embauchés
        $derniersEmployes = Employe::with('poste', 'departement')->orderByDesc('created_at')->take(5)->get();

        // Derniers congés en attente
        $congesRecents = Conge::with('employee')->orderByDesc('created_at')->take(5)->get();

        // Évolution de la masse salariale sur 6 mois
        $evolutionPaie = [];
        for ($i = 5; $i >= 0; $i--) {
            $period = now()->subMonths($i)->format('Y-m');
            $evolutionPaie[$period] = BulletinPaie::where('period', $period)->sum('salaire_net');
        }

        return view('dashboard', compact(
            'totalEmployes', 'actifs', 'departements', 'congesEnAttente',
            'parDepartement', 'masseSalariale', 'bulletinCourant',
            'presencesJour', 'presentJour', 'absentJour', 'statuts',
            'derniersEmployes', 'congesRecents', 'evolutionPaie'
        ));
    }

    private function employeDashboard($user)
    {
        $employe = $user->employe;

        $conges = [];
        $derniersBulletins = collect();
        $mesPresences = collect();

        if ($employe) {
            $conges = Conge::where('employee_id', $employe->id)
                ->where('date_fin', '>=', now())
                ->with('employee')
                ->orderBy('date_debut')
                ->take(5)
                ->get();

            $derniersBulletins = BulletinPaie::where('employee_id', $employe->id)
                ->orderByDesc('period')
                ->take(3)
                ->get();

            $mesPresences = Presence::where('employee_id', $employe->id)
                ->orderByDesc('date')
                ->take(10)
                ->get();
        }

        return view('dashboard-employe', compact('employe', 'conges', 'derniersBulletins', 'mesPresences'));
    }
}