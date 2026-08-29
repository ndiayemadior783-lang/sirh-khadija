<?php

namespace App\Http\Controllers;

use App\Models\Contrat;
use App\Models\Employe;
use Illuminate\Http\Request;

class ContratController extends Controller
{
    public function index(Request $request)
    {
        $query = Contrat::with('employee');

        if ($request->filled('statut')) {
            $query->where('statut', $request->input('statut'));
        }
        if ($request->filled('type')) {
            $query->where('type', $request->input('type'));
        }

        $contrats = $query->orderByDesc('date_debut')->paginate(12)->withQueryString();
        $employesActifs = Employe::where('statut', 'actif')->orderBy('prenom')->get();

        return view('contrats.index', compact('contrats', 'employesActifs'));
    }

    public function create()
    {
        $employes = Employe::where('statut', 'actif')->orderBy('prenom')->get();
        return view('contrats.create', compact('employes'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'employee_id' => ['required', 'exists:employees,id'],
            'type' => ['required', 'in:CDI,CDD,stage,freelance'],
            'date_debut' => ['required', 'date'],
            'date_fin' => ['nullable', 'date', 'after_or_equal:date_debut'],
            'salaire_base' => ['required', 'numeric', 'min:0'],
            'statut' => ['required', 'in:en_cours,termine'],
        ]);

        Contrat::create($data);

        return redirect()->route('contrats.index')->with('success', 'Contrat enregistré avec succès.');
    }

    public function show(Contrat $contrat)
    {
        $contrat->load('employee');
        return view('contrats.show', compact('contrat'));
    }

    public function destroy(Contrat $contrat)
    {
        $contrat->delete();
        return redirect()->route('contrats.index')->with('success', 'Contrat supprimé.');
    }
}