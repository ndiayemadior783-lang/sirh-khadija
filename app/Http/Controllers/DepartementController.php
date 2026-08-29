<?php

namespace App\Http\Controllers;

use App\Models\Departement;
use Illuminate\Http\Request;

class DepartementController extends Controller
{
    public function index()
    {
        $departements = Departement::withCount('employes')->withCount('postes')->orderBy('nom')->get();
        $total = Departement::count();
        $totalEmployes = \App\Models\Employe::count();

        return view('departements.index', compact('departements', 'total', 'totalEmployes'));
    }

    public function create()
    {
        return view('departements.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nom' => ['required', 'string', 'max:100', 'unique:departements,nom'],
            'description' => ['nullable', 'string', 'max:500'],
            'responsable' => ['nullable', 'string', 'max:150'],
        ]);

        Departement::create($data);

        return redirect()->route('departements.index')->with('success', 'Département créé.');
    }

    public function destroy(Departement $departement)
    {
        $departement->delete();
        return redirect()->route('departements.index')->with('success', 'Département supprimé.');
    }
}