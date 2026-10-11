<?php

namespace App\Http\Controllers;

use App\Models\Employe;
use App\Models\Parametre;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class RegistreController extends Controller
{
    public function index()
    {
        $param = Parametre::instance();
        $employes = Employe::with('poste', 'departement')->orderBy('date_embauche')->get();

        return view('registre.index', compact('param', 'employes'));
    }

    public function pdf()
    {
        $param = Parametre::instance();
        $employes = Employe::with('poste', 'departement')->orderBy('date_embauche')->get();

        $pdf = Pdf::loadView('exports.registre', compact('param', 'employes'))->setPaper('a4', 'landscape');
        return $pdf->download('registre-unique-personnel-' . now()->format('Y-m-d') . '.pdf');
    }

    public function csv()
    {
        $employes = Employe::with('poste', 'departement')->orderBy('date_embauche')->get();

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="registre-unique-personnel_' . now()->format('Y-m-d') . '.csv"',
        ];

        $callback = function () use ($employes) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['No', 'Prenoms et Nom', 'Date naissance', 'Lieu naissance', 'Sexe', 'Nationalite', 'N piece identite', 'Date embauche', 'Emploi', 'Departement', 'Salaire base', 'Adresse', 'Statut']);
            $i = 1;
            foreach ($employes as $e) {
                fputcsv($file, [
                    $i++, $e->nomComplet,
                    $e->date_naissance?->format('d/m/Y') ?? '',
                    $e->lieu_naissance ?? '',
                    $e->genre === 'M' ? 'Masculin' : 'Feminin',
                    $e->nationalite ?? '',
                    $e->cin ?? '',
                    $e->date_embauche?->format('d/m/Y') ?? '',
                    $e->poste?->libelle ?? '',
                    $e->departement?->nom ?? '',
                    $e->salaire_base,
                    trim(($e->adresse ?? '') . ' ' . ($e->ville ?? '')),
                    $e->statut,
                ]);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}