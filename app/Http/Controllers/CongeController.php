<?php

namespace App\Http\Controllers;

use App\Models\Conge;
use App\Models\Employe;
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

        return view('conges.index', compact('conges', 'enAttente'));
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

        return redirect()->route('conges.index')->with('success', 'Demande de congé soumise.');
    }

    public function valider(Request $request, Conge $conge)
    {
        $conge->update([
            'statut' => 'valide',
            'valide_par' => $request->user()->id,
            'date_validation' => now(),
        ]);

        return back()->with('success', 'Congé validé.');
    }

    public function rejeter(Request $request, Conge $conge)
    {
        $conge->update([
            'statut' => 'refuse',
            'valide_par' => $request->user()->id,
            'date_validation' => now(),
        ]);

        return back()->with('success', 'Congé rejeté.');
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