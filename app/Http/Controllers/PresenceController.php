<?php

namespace App\Http\Controllers;

use App\Models\Employe;
use App\Models\Presence;
use Illuminate\Http\Request;

class PresenceController extends Controller
{
    public function index(Request $request)
    {
        $date = $request->query('date', now()->toDateString());

        $presences = Presence::with('employee')
            ->where('date', $date)
            ->orderBy('employee_id')
            ->get();

        $present = $presences->where('statut', 'present')->count();
        $absent = $presences->where('statut', 'absent')->count();
        $conge = $presences->whereIn('statut', ['conge', 'maladie'])->count();
        $taux = $presences->count() > 0
            ? round($present / $presences->count() * 100, 1)
            : 0;

        return view('presences.index', compact('presences', 'date', 'present', 'absent', 'conge', 'taux'));
    }

    public function create()
    {
        $employes = Employe::where('statut', 'actif')->orderBy('prenom')->get();
        return view('presences.create', compact('employes'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'employee_id' => ['required', 'exists:employees,id'],
            'date' => ['required', 'date'],
            'heure_arrivee' => ['nullable'],
            'heure_depart' => ['nullable'],
            'statut' => ['required', 'in:present,absent,conge,maladie,teletravail'],
            'heures_sup' => ['nullable', 'numeric', 'min:0', 'max:24'],
            'notes' => ['nullable', 'string', 'max:500'],
        ]);

        Presence::updateOrCreate(
            ['employee_id' => $data['employee_id'], 'date' => $data['date']],
            $data
        );

        activity()->performedOn(Presence::latest()->first())->causedBy($request->user())->withProperties(['data' => $data])->log('Presence enregistree');

        return redirect()->route('presences.index', ['date' => $data['date']])
            ->with('success', 'Presence enregistree.');
    }

    public function destroy(Request $request, Presence $presence)
    {
        activity()->performedOn($presence)->causedBy($request->user())->log('Pointage supprime');

        $presence->delete();

        return redirect()->route('presences.index')->with('success', 'Pointage supprime.');
    }
}