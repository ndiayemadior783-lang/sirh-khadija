<?php

namespace App\Http\Controllers;

use App\Models\BulletinPaie;
use App\Models\Conge;
use App\Models\Employe;
use App\Models\Parametre;
use App\Models\Presence;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class ExportController extends Controller
{
    public function bulletin(BulletinPaie $bulletin)
    {
        $bulletin->load('employee.poste', 'employee.departement');
        $pdf = Pdf::loadView('exports.bulletin', compact('bulletin'));
        return $pdf->download('bulletin-' . $bulletin->numero . '.pdf');
    }

    public function livrePaie(Request $request)
    {
        $period = $request->query('period', now()->format('Y-m'));
        $mois = (int) substr($period, 5, 2);
        $nomMois = ['','Janvier','Fevrier','Mars','Avril','Mai','Juin','Juillet','Aout','Septembre','Octobre','Novembre','Decembre'][$mois] ?? '';
        $annee = substr($period, 0, 4);

        $bulletins = BulletinPaie::with('employee.poste', 'employee.departement')
            ->where('period', $period)->orderBy('employee_id')->get();

        $totaux = [
            'base' => $bulletins->sum('salaire_base'),
            'primes' => $bulletins->sum('primes'),
            'brut' => $bulletins->sum('salaire_brut'),
            'cotisations' => $bulletins->sum('cotisations_sociales'),
            'impots' => $bulletins->sum('impots'),
            'net' => $bulletins->sum('salaire_net'),
        ];

        $pdf = Pdf::loadView('exports.livre-paie', compact('bulletins', 'totaux', 'nomMois', 'annee'));
        return $pdf->download("livre-paie-{$period}.pdf");
    }

    public function etatCotisations(Request $request)
    {
        $period = $request->query('period', now()->format('Y-m'));
        $mois = (int) substr($period, 5, 2);
        $nomMois = ['','Janvier','Fevrier','Mars','Avril','Mai','Juin','Juillet','Aout','Septembre','Octobre','Novembre','Decembre'][$mois] ?? '';
        $annee = substr($period, 0, 4);

        $bulletins = BulletinPaie::with('employee')
            ->where('period', $period)->orderBy('employee_id')->get();

        $pdf = Pdf::loadView('exports.cotisations', compact('bulletins', 'nomMois', 'annee'));
        return $pdf->download("etat-cotisations-{$period}.pdf");
    }

    public function listeEmployes(Request $request)
    {
        $query = Employe::with('poste', 'departement');
        if ($request->filled('departement_id')) $query->where('departement_id', $request->input('departement_id'));
        if ($request->filled('statut')) $query->where('statut', $request->input('statut'));
        $employes = $query->orderBy('nom')->get();

        $pdf = Pdf::loadView('exports.liste-employes', compact('employes'));
        return $pdf->download('liste-employes.pdf');
    }

    // ============ ATTESTATIONS ============

    public function listeAttestations()
    {
        $employes = Employe::with('poste', 'departement')->orderBy('nom')->orderBy('prenom')->get();
        $pdf = Pdf::loadView('exports.liste-attestations', compact('employes'));
        return $pdf->download('liste-attestations.pdf');
    }

    public function attestationsIndex()
    {
        $employes = Employe::with('poste', 'departement')->orderBy('nom')->orderBy('prenom')->get();
        return view('attestations.index', compact('employes'));
    }

    public function attestationTravail(Employe $employe)
    {
        $employe->load('poste', 'departement');
        $param = Parametre::instance();

        $dateEmbauche = $employe->date_embauche ? \Carbon\Carbon::parse($employe->date_embauche) : now();
        $duree = $dateEmbauche->diffInMonths(now());

        $pdf = Pdf::loadView('exports.attestation-travail', compact('employe', 'param', 'dateEmbauche', 'duree'));
        return $pdf->download('attestation-travail-' . $employe->nom . '-' . $employe->prenom . '.pdf');
    }

    public function attestationSalaire(Employe $employe)
    {
        $employe->load('poste', 'departement');
        $param = Parametre::instance();

        $dernierBulletin = $employe->bulletins()->orderByDesc('period')->first();

        $pdf = Pdf::loadView('exports.attestation-salaire', compact('employe', 'param', 'dernierBulletin'));
        return $pdf->download('attestation-salaire-' . $employe->nom . '-' . $employe->prenom . '.pdf');
    }

    public function certificatTravail(Employe $employe)
    {
        $employe->load('poste', 'departement');
        $param = Parametre::instance();

        $dateEmbauche = $employe->date_embauche ? \Carbon\Carbon::parse($employe->date_embauche) : now();

        $pdf = Pdf::loadView('exports.certificat-travail', compact('employe', 'param', 'dateEmbauche'));
        return $pdf->download('certificat-travail-' . $employe->nom . '-' . $employe->prenom . '.pdf');
    }

    // ============ CSV ============

    public function employesCsv()
    {
        $employes = Employe::with('poste', 'departement')->orderBy('nom')->get();

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="employes_' . now()->format('Y-m-d') . '.csv"',
        ];

        $callback = function () use ($employes) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['Matricule', 'Nom', 'Prenom', 'Email', 'Telephone', 'Poste', 'Departement', 'Salaire base', 'Date embauche', 'Statut']);
            foreach ($employes as $e) {
                fputcsv($file, [
                    $e->matricule, $e->nom, $e->prenom, $e->email, $e->telephone,
                    $e->poste?->libelle ?? '', $e->departement?->nom ?? '',
                    $e->salaire_base, $e->date_embauche?->format('d/m/Y'), $e->statut,
                ]);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function bulletinsCsv(Request $request)
    {
        $period = $request->query('period', now()->format('Y-m'));
        $bulletins = BulletinPaie::with('employee')->where('period', $period)->orderBy('employee_id')->get();

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="bulletins_' . $period . '.csv"',
        ];

        $callback = function () use ($bulletins, $period) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['Numero', 'Employe', 'Matricule', 'Salaire base', 'Primes', 'Heures sup', 'Indemnites', 'Brut', 'Cotisations', 'Impots', 'Net', 'Periode']);
            foreach ($bulletins as $b) {
                fputcsv($file, [
                    $b->numero, $b->employee?->nomComplet, $b->employee?->matricule,
                    $b->salaire_base, $b->primes, $b->heures_sup, $b->indemnites,
                    $b->salaire_brut, $b->cotisations_sociales, $b->impots, $b->salaire_net, $period,
                ]);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function congesCsv()
    {
        $conges = Conge::with('employee')->orderByDesc('created_at')->get();

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="conges_' . now()->format('Y-m-d') . '.csv"',
        ];

        $callback = function () use ($conges) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['Employe', 'Type', 'Date debut', 'Date fin', 'Jours', 'Motif', 'Statut', 'Demande le']);
            foreach ($conges as $c) {
                fputcsv($file, [
                    $c->employee?->nomComplet, $c->type, $c->date_debut, $c->date_fin,
                    $c->jours, $c->motif, $c->statut, $c->created_at?->format('d/m/Y'),
                ]);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function presencesCsv(Request $request)
    {
        $date = $request->query('date', now()->toDateString());
        $presences = Presence::with('employee')->where('date', $date)->orderBy('employee_id')->get();

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="presences_' . $date . '.csv"',
        ];

        $callback = function () use ($presences, $date) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['Employe', 'Matricule', 'Statut', 'Heure arrivee', 'Heure depart', 'Heures sup', 'Notes']);
            foreach ($presences as $p) {
                fputcsv($file, [
                    $p->employee?->nomComplet, $p->employee?->matricule, $p->statut,
                    $p->heure_arrivee, $p->heure_depart, $p->heures_sup, $p->notes,
                ]);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}