<?php

namespace App\Http\Controllers;

use App\Models\Employe;
use App\Models\Prime;
use Illuminate\Http\Request;

class PrimeController extends Controller
{
    public function index(Request $request)
    {
        $query = Prime::with('employee');

        if ($request->filled('employee_id')) {
            $query->where('employee_id', $request->input('employee_id'));
        }

        $primes = $query->orderByDesc('date')->paginate(15)->withQueryString();
        $totaux = Prime::selectRaw('sum(montant) as total, count(*) as nombre')->first();
        $employes = Employe::where('statut', 'actif')->orderBy('prenom')->get();

        return view('primes.index', compact('primes', 'totaux', 'employes'));
    }

    public function create()
    {
        $employes = Employe::where('statut', 'actif')->orderBy('prenom')->get();
        return view('primes.create', compact('employes'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'employee_id' => ['required', 'exists:employees,id'],
            'libelle' => ['required', 'string', 'max:150'],
            'montant' => ['required', 'numeric', 'min:0'],
            'date' => ['required', 'date'],
        ]);

        Prime::create($data);

        activity()->performedOn(Prime::latest()->first())->causedBy($request->user())->withProperties(['data' => $data])->log('Prime enregistree');

        return redirect()->route('primes.index')->with('success', 'Prime enregistree.');
    }

    public function destroy(Request $request, Prime $prime)
    {
        activity()->performedOn($prime)->causedBy($request->user())->log('Prime supprimee');

        $prime->delete();

        return redirect()->route('primes.index')->with('success', 'Prime supprimee.');
    }
}