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

        activity()->performedOn(Departement::latest()->first())->causedBy($request->user())->withProperties(['data' => $data])->log('Departement cree');

        return redirect()->route('departements.index')->with('success', 'Departement cree.');
    }

    public function destroy(Request $request, Departement $departement)
    {
        activity()->performedOn($departement)->causedBy($request->user())->log('Departement supprime');

        $departement->delete();

        return redirect()->route('departements.index')->with('success', 'Departement supprime.');
    }
}