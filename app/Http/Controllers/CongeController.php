<?php

namespace App\Http\Controllers;

use App\Models\Conge;
use App\Models\Employe;
use App\Models\Parametre;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class CongeController extends Controller
{
    public function index(Request $request)
    {
        $query = Conge::with('employee', 'valideur');

        if ($request->user()->role === 'employe' && $request->user()->employe) {
            $query->where('employee_id', $request->user()->employe->id);
        }

        if ($request->filled('statut')) {
            $query->where('statut', $request->input('statut'));
        }

        $conges = $query->orderByDesc('created_at')->paginate(12)->withQueryString();
        $enAttente = $conges->where('statut', 'en_attente')->count();
        $employesActifs = $request->user()->estGestion()
            ? Employe::where('statut', 'actif')->orderBy('prenom')->get()
            : collect();

        $droitAnnuel = (int) (Parametre::instance()->jours_conge_paye ?: 22);

        return view('conges.index', compact('conges', 'enAttente', 'employesActifs', 'droitAnnuel'));
    }

    public function create(Request $request)
    {
        $employes = collect();
        if ($request->user()->estGestion()) {
            $employes = Employe::where('statut', 'actif')->orderBy('prenom')->get();
        }
        return view('conges.create', compact('employes'));
    }

    public function store(Request $request)
    {
        $data = $this->validateConge($request);

        $debut = \Carbon\Carbon::parse($data['date_debut']);
        $fin = \Carbon\Carbon::parse($data['date_fin']);
        $data['jours'] = $debut->diffInDays($fin) + 1;
        $data['statut'] = 'en_attente';

        Conge::create($data);

        activity()->performedOn(Conge::latest()->first())->causedBy($request->user())->withProperties(['data' => $data])->log('Demande de conge creee');

        return redirect()->route('conges.index')->with('success', 'Demande de conge soumise.');
    }

    public function valider(Request $request, Conge $conge)
    {
        if ($conge->type === 'paye') {
            $solde = $conge->employee?->soldeConges() ?? 0;
            if ($solde < $conge->jours) {
                return back()->with('error', 'Validation impossible : solde de congé payé insuffisant (reste ' . $solde . ' jour(s), demande : ' . $conge->jours . ' jour(s)).');
            }
        }

        $conge->update([
            'statut' => 'valide',
            'valide_par' => $request->user()->id,
            'date_validation' => now(),
        ]);

        activity()->performedOn($conge)->causedBy($request->user())->log('Conge valide');

        return back()->with('success', 'Conge valide.');
    }

    public function rejeter(Request $request, Conge $conge)
    {
        $conge->update([
            'statut' => 'refuse',
            'valide_par' => $request->user()->id,
            'date_validation' => now(),
        ]);

        activity()->performedOn($conge)->causedBy($request->user())->log('Conge rejete');

        return back()->with('success', 'Conge rejete.');
    }

    public function planning(Request $request)
    {
        $data = $this->planningData(
            (int) $request->query('mois', now()->month),
            (int) $request->query('annee', now()->year)
        );

        return view('conges.planning', $data);
    }

    public function planningPdf(Request $request)
    {
        $data = $this->planningData(
            (int) $request->query('mois', now()->month),
            (int) $request->query('annee', now()->year)
        );

        $pdf = Pdf::loadView('exports.planning-conges', $data)->setPaper('a4', 'landscape');
        return $pdf->download('planning-conges-' . $data['annee'] . '-' . str_pad((string) $data['mois'], 2, '0', STR_PAD_LEFT) . '.pdf');
    }

    private function planningData(int $mois, int $annee): array
    {
        $mois = max(1, min(12, $mois));
        $annee = max(2000, min(2100, $annee));

        $debut = \Carbon\Carbon::create($annee, $mois, 1, 0, 0, 0);
        $fin = (clone $debut)->endOfMonth()->endOfDay();

        $conges = Conge::with('employee')
            ->where(function ($q) use ($debut, $fin) {
                $q->whereBetween('date_debut', [$debut, $fin])
                    ->orWhereBetween('date_fin', [$debut, $fin])
                    ->orWhere(function ($q2) use ($debut, $fin) {
                        $q2->where('date_debut', '<=', $debut)
                            ->where('date_fin', '>=', $fin);
                    });
            })
            ->orderBy('date_debut')
            ->get();

        $eventsByDay = [];
        foreach ($conges as $conge) {
            $start = $conge->date_debut->copy()->startOfDay();
            $end = $conge->date_fin->copy()->startOfDay();
            if ($start->lt($debut)) {
                $start = $debut->copy();
            }
            if ($end->gt($fin)) {
                $end = $fin->copy();
            }
            for ($d = $start; $d->lte($end); $d->addDay()) {
                $eventsByDay[$d->format('Y-m-d')][] = $conge;
            }
        }

        $nomsMois = ['Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin', 'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre'];

        return [
            'mois' => $mois,
            'annee' => $annee,
            'nomMois' => $nomsMois[$mois - 1],
            'debut' => $debut,
            'fin' => $fin,
            'eventsByDay' => $eventsByDay,
            'societe' => Parametre::instance()->nom_entreprise,
            'statsValides' => $conges->where('statut', 'valide')->count(),
            'statsAttente' => $conges->where('statut', 'en_attente')->count(),
            'statsRefuses' => $conges->where('statut', 'refuse')->count(),
        ];
    }

    private function validateConge(Request $request): array
    {
        $rules = [
            'type' => ['required', 'in:paye,maladie,maternite,sans_solde,exceptionnel'],
            'date_debut' => ['required', 'date'],
            'date_fin' => ['required', 'date', 'after_or_equal:date_debut'],
            'motif' => ['nullable', 'string', 'max:1000'],
        ];

        if ($request->user()->estGestion()) {
            $rules['employee_id'] = ['required', 'exists:employees,id'];
        } else {
            $rules['employee_id'] = ['nullable'];
        }

        $data = $request->validate($rules);

        if (!$request->user()->estGestion()) {
            $data['employee_id'] = $request->user()->employe?->id;
        }

        return $data;
    }
}