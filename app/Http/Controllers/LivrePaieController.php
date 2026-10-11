<?php

namespace App\Http\Controllers;

use App\Models\BulletinPaie;
use App\Models\Employe;
use App\Models\Parametre;
use Illuminate\Http\Request;

class LivrePaieController extends Controller
{
    public function livrePaie(Request $request)
    {
        $period = $request->query('period', now()->format('Y-m'));
        $annee = substr($period, 0, 4);
        $mois = (int) substr($period, 5, 2);
        $nomMois = $this->nomMois($mois);

        $bulletins = BulletinPaie::with('employee.poste', 'employee.departement')
            ->where('period', $period)
            ->orderBy('employee_id')
            ->get();

        $totalBrut = $bulletins->sum('salaire_brut');
        $totalCotisations = $bulletins->sum('cotisations_sociales');
        $totalImpots = $bulletins->sum('impots');
        $totalNet = $bulletins->sum('salaire_net');
        $totalPrimes = $bulletins->sum('primes');
        $totalHS = $bulletins->sum('heures_sup');
        $totalIndemnites = $bulletins->sum('indemnites');
        $totalBase = $bulletins->sum('salaire_base');
        $nombreEmployes = $bulletins->count();

        $periodes = BulletinPaie::selectRaw('period, count(*) as total')
            ->groupBy('period')
            ->orderByDesc('period')
            ->get();

        return view('livre_paie.index', compact(
            'bulletins', 'period', 'annee', 'nomMois',
            'totalBrut', 'totalCotisations', 'totalImpots', 'totalNet',
            'totalPrimes', 'totalHS', 'totalIndemnites', 'totalBase',
            'nombreEmployes', 'periodes'
        ));
    }

    public function etatCotisations(Request $request)
    {
        $period = $request->query('period', now()->format('Y-m'));
        $annee = substr($period, 0, 4);
        $mois = (int) substr($period, 5, 2);
        $nomMois = $this->nomMois($mois);

        // Taux depuis les parametres
        $param = Parametre::instance();
        $taux = [
            'ipres_employeur'  => $param->ipres_employeur,
            'ipres_employe'    => $param->ipres_employe,
            'ipm_employeur'    => $param->ipm_employeur,
            'ipm_employe'      => $param->ipm_employe,
            'cfrp_employeur'   => $param->cfrp_employeur,
            'cfrp_employe'     => $param->cfrp_employe,
            'taxe_exceptionnelle' => $param->taxe_exceptionnelle,
        ];

        $bulletins = BulletinPaie::with('employee.poste', 'employee.departement')
            ->where('period', $period)
            ->orderBy('employee_id')
            ->get();

        $lignes = $bulletins->map(function ($b) use ($taux) {
            $brut = $b->salaire_brut;

            $ipresEmploi = round($brut * $taux['ipres_employeur'], 0);
            $ipresEmplo  = round($brut * $taux['ipres_employe'], 0);
            $ipmEmploi   = round($brut * $taux['ipm_employeur'], 0);
            $ipmEmplo    = round($brut * $taux['ipm_employe'], 0);
            $cfrpEmploi  = round($brut * $taux['cfrp_employeur'], 0);
            $cfrpEmplo   = round($brut * $taux['cfrp_employe'], 0);
            $taxeExcep   = round($brut * $taux['taxe_exceptionnelle'], 0);
            $totalCotisEmploi = $ipresEmploi + $ipmEmploi + $cfrpEmploi;
            $totalCotisEmplo  = $ipresEmplo + $ipmEmplo + $cfrpEmplo;

            return (object) [
                'employee'           => $b->employee,
                'salaire_base'       => $b->salaire_base,
                'primes'             => $b->primes,
                'salaire_brut'       => $brut,
                'ipres_employeur'    => $ipresEmploi,
                'ipres_employe'      => $ipresEmplo,
                'ipm_employeur'      => $ipmEmploi,
                'ipm_employe'        => $ipmEmplo,
                'cfrp_employeur'     => $cfrpEmploi,
                'cfrp_employe'       => $cfrpEmplo,
                'taxe_exceptionnelle'=> $taxeExcep,
                'total_employeur'    => $totalCotisEmploi + $taxeExcep,
                'total_employe'      => $totalCotisEmplo,
                'impot_revenu'       => $b->impots,
                'salaire_net'        => $b->salaire_net,
            ];
        });

        $totaux = [
            'brut'       => $lignes->sum('salaire_brut'),
            'ipres_employeur'    => $lignes->sum('ipres_employeur'),
            'ipres_employe'      => $lignes->sum('ipres_employe'),
            'ipm_employeur'      => $lignes->sum('ipm_employeur'),
            'ipm_employe'        => $lignes->sum('ipm_employe'),
            'cfrp_employeur'     => $lignes->sum('cfrp_employeur'),
            'cfrp_employe'       => $lignes->sum('cfrp_employe'),
            'taxe_exceptionnelle'=> $lignes->sum('taxe_exceptionnelle'),
            'total_employeur'    => $lignes->sum('total_employeur'),
            'total_employe'      => $lignes->sum('total_employe'),
            'impot_revenu'       => $lignes->sum('impot_revenu'),
            'total_deductions'   => $lignes->sum('total_employe') + $lignes->sum('impot_revenu'),
            'total_net'          => $lignes->sum('salaire_net'),
        ];

        $periodes = BulletinPaie::selectRaw('period, count(*) as total')
            ->groupBy('period')
            ->orderByDesc('period')
            ->get();

        return view('cotisations.index', compact(
            'lignes', 'totaux', 'period', 'annee', 'nomMois', 'taux', 'periodes'
        ));
    }

    private function nomMois(int $numMois): string
    {
        $mois = [
            1 => 'Janvier', 2 => 'Fevrier', 3 => 'Mars',
            4 => 'Avril', 5 => 'Mai', 6 => 'Juin',
            7 => 'Juillet', 8 => 'Aout', 9 => 'Septembre',
            10 => 'Octobre', 11 => 'Novembre', 12 => 'Decembre',
        ];
        return $mois[$numMois] ?? '';
    }
}
