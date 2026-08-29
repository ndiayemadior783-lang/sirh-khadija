<?php

namespace App\Http\Controllers;

use App\Models\BulletinPaie;
use App\Models\Employe;
use App\Models\Presence;
use App\Models\Prime;
use Illuminate\Http\Request;

class BulletinPaieController extends Controller
{
    private const TAUX_COTISATIONS = 0.06;
    private const TAUX_IMPOT = 0.08;
    private const TAUX_HORAIRE = 1500;

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

        $employes = Employe::where('statut', 'actif')->get();
        $cree = 0;

        foreach ($employes as $employe) {
            if (BulletinPaie::where('employee_id', $employe->id)->where('period', $period)->exists()) {
                continue;
            }

            $primes = Prime::where('employee_id', $employe->id)
                ->whereRaw("DATE_FORMAT(date, '%Y-%m') = ?", [$period])
                ->sum('montant');

            $heuresSup = Presence::where('employee_id', $employe->id)
                ->whereRaw("DATE_FORMAT(date, '%Y-%m') = ?", [$period])
                ->sum('heures_sup');

            $montantHeuresSup = round($heuresSup * self::TAUX_HORAIRE, 2);
            $brut = $employe->salaire_base + $primes + $montantHeuresSup;
            $cotisations = round($brut * self::TAUX_COTISATIONS, 2);
            $impots = round(($brut - $cotisations) * self::TAUX_IMPOT, 2);
            $net = $brut - $cotisations - $impots;

            BulletinPaie::create([
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
        }

        $message = $cree > 0
            ? "{$cree} bulletin(s) gÃ©nÃ©rÃ©(s) pour la pÃ©riode {$period}."
            : 'Tous les bulletins existent dÃ©jÃ  pour cette pÃ©riode.';

        return redirect()->route('bulletins.index', ['period' => $period])->with('success', $message);
    }

    public function show(BulletinPaie $bulletin)
    {
        $bulletin->load('employee.poste', 'employee.departement');
        return view('bulletins.show', compact('bulletin'));
    }
}