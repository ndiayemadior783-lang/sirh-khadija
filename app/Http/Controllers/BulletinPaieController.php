<?php

namespace App\Http\Controllers;

use App\Models\BulletinPaie;
use App\Models\Employe;
use App\Models\Parametre;
use App\Models\Presence;
use App\Models\Prime;
use Illuminate\Http\Request;

class BulletinPaieController extends Controller
{
    public function index(Request $request)
    {
        $period = $request->query('period', now()->format('Y-m'));

        $bulletins = BulletinPaie::with('employee')
            ->where('period', $period)
            ->orderBy('employee_id')
            ->get();

        $totalBrut = $bulletins->sum('salaire_brut');
        $totalNet = $bulletins->sum('salaire_net');
        $totalCotisations = $bulletins->sum('cotisations_sociales') + $bulletins->sum('impots');
        $periodes = BulletinPaie::selectRaw('period, count(*) as total')->groupBy('period')->orderByDesc('period')->get();

        return view('bulletins.index', compact('bulletins', 'period', 'totalBrut', 'totalNet', 'totalCotisations', 'periodes'));
    }

    public function generer(Request $request)
    {
        $period = $request->input('period', now()->format('Y-m'));

        $param = Parametre::instance();
        $tauxCotisation = $param->taux_cotisation;
        $tauxImpot = $param->taux_impot;
        $tauxHoraire = $param->taux_horaire;

        $employes = Employe::where('statut', 'actif')->get();
        $cree = 0;

        foreach ($employes as $employe) {
            if (BulletinPaie::where('employee_id', $employe->id)->where('period', $period)->exists()) {
                continue;
            }

            $primes = Prime::where('employee_id', $employe->id)
                ->whereYear('date', substr($period, 0, 4))
                ->whereMonth('date', (int) substr($period, 5, 2))
                ->sum('montant');

            $heuresSup = Presence::where('employee_id', $employe->id)
                ->whereYear('date', substr($period, 0, 4))
                ->whereMonth('date', (int) substr($period, 5, 2))
                ->sum('heures_sup');

            $montantHeuresSup = round($heuresSup * $tauxHoraire, 2);
            $brut = $employe->salaire_base + $primes + $montantHeuresSup;
            $cotisations = round($brut * $tauxCotisation, 2);
            $impots = round(($brut - $cotisations) * $tauxImpot, 2);
            $net = $brut - $cotisations - $impots;

            $bulletin = BulletinPaie::create([
                'numero' => 'BUL-' . str_replace('-', '', $period) . '-' . str_pad((string)(BulletinPaie::max('id') + 1), 4, '0', STR_PAD_LEFT),
                'employee_id' => $employe->id,
                'period' => $period,
                'salaire_base' => $employe->salaire_base,
                'primes' => $primes,
                'heures_sup' => $montantHeuresSup,
                'indemnites' => 0,
                'salaire_brut' => $brut,
                'cotisations_sociales' => $cotisations,
                'impots' => $impots,
                'salaire_net' => $net,
                'statut' => 'brouillon',
            ]);

            $cree++;

            activity()->performedOn($bulletin)->causedBy(auth()->user())->withProperties(['employe_id' => $employe->id, 'period' => $period])->log('Bulletin genere');
        }

        if ($cree > 0) {
            activity()->causedBy(auth()->user())->withProperties(['period' => $period, 'nombre' => $cree])->log('Generation de ' . $cree . ' bulletins pour ' . $period);
        }

        $message = $cree > 0
            ? "{$cree} bulletin(s) genere(s) pour la periode {$period}."
            : 'Tous les bulletins existent deja pour cette periode.';

        return redirect()->route('bulletins.index', ['period' => $period])->with('success', $message);
    }

    public function show(BulletinPaie $bulletin)
    {
        $bulletin->load('employee.poste', 'employee.departement');
        return view('bulletins.show', compact('bulletin'));
    }
}
