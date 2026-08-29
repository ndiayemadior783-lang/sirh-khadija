<?php

namespace App\Http\Controllers;

use App\Models\Employe;
use App\Models\Formation;
use Illuminate\Http\Request;

class FormationController extends Controller
{
    public function index()
    {
        $formations = Formation::withCount('employes')->orderByDesc('date_debut')->get();
        return view('formations.index', compact('formations'));
    }

    public function create()
    {
        return view('formations.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'titre' => ['required', 'string', 'max:150'],
            'description' => ['nullable', 'string', 'max:1000'],
            'date_debut' => ['nullable', 'date'],
            'date_fin' => ['nullable', 'date', 'after_or_equal:date_debut'],
            'formateur' => ['nullable', 'string', 'max:150'],
            'lieu' => ['nullable', 'string', 'max:150'],
            'cout' => ['required', 'numeric', 'min:0'],
        ]);

        Formation::create($data);

        return redirect()->route('formations.index')->with('success', 'Formation créée.');
    }

    public function show(Formation $formation)
    {
        $formation->load('employes');
        $employesDisponibles = Employe::where('statut', 'actif')->orderBy('prenom')->get();
        return view('formations.show', compact('formation', 'employesDisponibles'));
    }

    public function inscrire(Request $request, Formation $formation)
    {
        $data = $request->validate([
            'employee_id' => ['required', 'exists:employees,id'],
        ]);

        $formation->employes()->syncWithoutDetaching([
            $data['employee_id'] => ['statut' => 'inscrit'],
        ]);

        return back()->with('success', 'Employé inscrit à la formation.');
    }
}