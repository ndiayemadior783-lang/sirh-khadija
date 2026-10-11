<?php

namespace App\Http\Controllers;

use App\Models\BulletinPaie;
use App\Models\Contrat;
use App\Models\Conge;
use App\Models\Departement;
use App\Models\Employe;
use App\Models\Formation;
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

        $parDepartement = Employe::selectRaw('departements.nom, count(*) as total')
            ->join('departements', 'employees.departement_id', '=', 'departements.id')
            ->groupBy('departements.nom')
            ->orderByDesc('total')
            ->pluck('total', 'nom');

        $masseSalariale = Employe::where('statut', 'actif')->sum('salaire_base');
        $bulletinCourant = BulletinPaie::where('period', now()->format('Y-m'))->sum('salaire_net');

        $aujourdhui = now()->toDateString();
        $presencesJour = Presence::where('date', $aujourdhui)->count();
        $presentJour = Presence::where('date', $aujourdhui)->where('statut', 'present')->count();
        $absentJour = Presence::where('date', $aujourdhui)->where('statut', 'absent')->count();

        $statuts = Employe::selectRaw('statut, count(*) as total')->groupBy('statut')->pluck('total', 'statut');

        $derniersEmployes = Employe::with('poste', 'departement')->orderByDesc('created_at')->take(5)->get();

        $congesRecents = Conge::with('employee')->orderByDesc('created_at')->take(5)->get();

        $evolutionPaie = [];
        for ($i = 5; $i >= 0; $i--) {
            $period = now()->subMonths($i)->format('Y-m');
            $evolutionPaie[$period] = BulletinPaie::where('period', $period)->sum('salaire_net');
        }

        // Contrats qui expirent dans les 30 prochains jours
        $contratsExpirants = Contrat::with('employee')
            ->where('statut', 'en_cours')
            ->whereNotNull('date_fin')
            ->whereBetween('date_fin', [now(), now()->addDays(30)])
            ->orderBy('date_fin')
            ->take(5)
            ->get();

        // Statistiques enrichies
        $parGenre = Employe::selectRaw('genre, count(*) as total')->groupBy('genre')->pluck('total', 'genre');
        $embauchesMois = Employe::whereYear('date_embauche', now()->year)->whereMonth('date_embauche', now()->month)->count();
        $congesValidesMois = Conge::whereYear('date_debut', now()->year)->whereMonth('date_debut', now()->month)->where('statut', 'valide')->count();
        $congesAttenteMois = Conge::whereYear('date_debut', now()->year)->whereMonth('date_debut', now()->month)->where('statut', 'en_attente')->count();
        $congesRefusesMois = Conge::whereYear('date_debut', now()->year)->whereMonth('date_debut', now()->month)->where('statut', 'refuse')->count();
        $formationsEnCours = Formation::where('date_debut', '<=', now())->where('date_fin', '>=', now())->count();
        $nombreCongesMois = $congesValidesMois + $congesAttenteMois + $congesRefusesMois;

        return view('dashboard', compact(
            'totalEmployes', 'actifs', 'departements', 'congesEnAttente',
            'parDepartement', 'masseSalariale', 'bulletinCourant',
            'presencesJour', 'presentJour', 'absentJour', 'statuts',
            'derniersEmployes', 'congesRecents', 'evolutionPaie',
            'contratsExpirants', 'parGenre', 'embauchesMois',
            'congesValidesMois', 'congesAttenteMois', 'congesRefusesMois',
            'formationsEnCours', 'nombreCongesMois'
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