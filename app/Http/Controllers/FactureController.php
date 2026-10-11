<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Facture;
use App\Models\FactureLigne;
use App\Models\Parametre;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FactureController extends Controller
{
    public function index(Request $request)
    {
        $query = Facture::with('client');

        if ($request->filled('statut')) {
            $query->where('statut', $request->input('statut'));
        }

        if ($request->filled('client_id')) {
            $query->where('client_id', $request->input('client_id'));
        }

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('numero', 'like', "%{$search}%")
                    ->orWhereHas('client', function ($qc) use ($search) {
                        $qc->where('nom', 'like', "%{$search}%");
                    });
            });
        }

        $factures = $query->orderByDesc('date_facture')->orderByDesc('id')->paginate(12)->withQueryString();

        // Statistiques globales
        $totalFacture = Facture::where('statut', '!=', 'annulee')->sum('total_ttc');
        $totalPaye = Facture::where('statut', 'payee')->sum('total_ttc');
        $totalEnAttente = Facture::where('statut', 'emise')->sum('total_ttc');
        $totalBrouillon = Facture::where('statut', 'brouillon')->sum('total_ttc');

        $clients = Client::orderBy('nom')->get();

        return view('factures.index', compact(
            'factures',
            'totalFacture',
            'totalPaye',
            'totalEnAttente',
            'totalBrouillon',
            'clients'
        ));
    }

    public function create()
    {
        $clients = Client::orderBy('nom')->get();
        $numeroPropose = Facture::genererNumero();

        return view('factures.create', compact('clients', 'numeroPropose'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'numero' => ['required', 'string', 'max:50', 'unique:factures,numero'],
            'client_id' => ['required', 'exists:clients,id'],
            'date_facture' => ['required', 'date'],
            'date_echeance' => ['nullable', 'date', 'after_or_equal:date_facture'],
            'statut' => ['required', 'in:brouillon,emise,payee,annulee'],
            'mode_paiement' => ['nullable', 'in:virement,cheque,especes,wave,orange_money'],
            'appliquer_tva' => ['nullable', 'boolean'],
            'taux_tva' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'conditions_reglement' => ['nullable', 'string', 'max:1000'],
            'notes' => ['nullable', 'string', 'max:1000'],
            'lignes' => ['required', 'array', 'min:1'],
            'lignes.*.designation' => ['required', 'string', 'max:255'],
            'lignes.*.quantite' => ['required', 'numeric', 'min:0.01'],
            'lignes.*.prix_unitaire' => ['required', 'numeric', 'min:0'],
        ]);

        DB::beginTransaction();
        try {
            $appliquerTva = $request->boolean('appliquer_tva', true);
            $tauxTva = $appliquerTva ? (float) ($data['taux_tva'] ?? 18.00) : 0.00;

            // Calcul du total HT
            $totalHt = 0;
            foreach ($data['lignes'] as $ligne) {
                $qte = (float) $ligne['quantite'];
                $pu = (float) $ligne['prix_unitaire'];
                $totalHt += ($qte * $pu);
            }

            $montantTva = $appliquerTva ? round($totalHt * ($tauxTva / 100), 2) : 0;
            $totalTtc = $totalHt + $montantTva;

            $facture = Facture::create([
                'numero' => $data['numero'],
                'client_id' => $data['client_id'],
                'date_facture' => $data['date_facture'],
                'date_echeance' => $data['date_echeance'] ?? null,
                'statut' => $data['statut'],
                'mode_paiement' => $data['mode_paiement'] ?? null,
                'date_paiement' => $data['statut'] === 'payee' ? now()->toDateString() : null,
                'appliquer_tva' => $appliquerTva,
                'taux_tva' => $tauxTva,
                'total_ht' => $totalHt,
                'montant_tva' => $montantTva,
                'total_ttc' => $totalTtc,
                'conditions_reglement' => $data['conditions_reglement'] ?? null,
                'notes' => $data['notes'] ?? null,
                'created_by' => $request->user()->id,
            ]);

            foreach ($data['lignes'] as $ligne) {
                $qte = (float) $ligne['quantite'];
                $pu = (float) $ligne['prix_unitaire'];
                FactureLigne::create([
                    'facture_id' => $facture->id,
                    'designation' => $ligne['designation'],
                    'quantite' => $qte,
                    'prix_unitaire' => $pu,
                    'total_ligne' => round($qte * $pu, 2),
                ]);
            }

            DB::commit();

            activity()->performedOn($facture)
                ->causedBy($request->user())
                ->withProperties(['numero' => $facture->numero, 'total_ttc' => $totalTtc])
                ->log('Facture créée');

            return redirect()->route('factures.show', $facture)->with('success', "Facture {$facture->numero} créée avec succès.");
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Erreur lors de la création de la facture : ' . $e->getMessage());
        }
    }

    public function show(Facture $facture)
    {
        $facture->load(['client', 'lignes', 'createur']);
        $param = Parametre::instance();

        return view('factures.show', compact('facture', 'param'));
    }

    public function edit(Facture $facture)
    {
        $facture->load(['client', 'lignes']);
        $clients = Client::orderBy('nom')->get();

        return view('factures.edit', compact('facture', 'clients'));
    }

    public function update(Request $request, Facture $facture)
    {
        $data = $request->validate([
            'client_id' => ['required', 'exists:clients,id'],
            'date_facture' => ['required', 'date'],
            'date_echeance' => ['nullable', 'date', 'after_or_equal:date_facture'],
            'statut' => ['required', 'in:brouillon,emise,payee,annulee'],
            'mode_paiement' => ['nullable', 'in:virement,cheque,especes,wave,orange_money'],
            'appliquer_tva' => ['nullable', 'boolean'],
            'taux_tva' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'conditions_reglement' => ['nullable', 'string', 'max:1000'],
            'notes' => ['nullable', 'string', 'max:1000'],
            'lignes' => ['required', 'array', 'min:1'],
            'lignes.*.designation' => ['required', 'string', 'max:255'],
            'lignes.*.quantite' => ['required', 'numeric', 'min:0.01'],
            'lignes.*.prix_unitaire' => ['required', 'numeric', 'min:0'],
        ]);

        DB::beginTransaction();
        try {
            $appliquerTva = $request->boolean('appliquer_tva', true);
            $tauxTva = $appliquerTva ? (float) ($data['taux_tva'] ?? 18.00) : 0.00;

            $totalHt = 0;
            foreach ($data['lignes'] as $ligne) {
                $qte = (float) $ligne['quantite'];
                $pu = (float) $ligne['prix_unitaire'];
                $totalHt += ($qte * $pu);
            }

            $montantTva = $appliquerTva ? round($totalHt * ($tauxTva / 100), 2) : 0;
            $totalTtc = $totalHt + $montantTva;

            $facture->update([
                'client_id' => $data['client_id'],
                'date_facture' => $data['date_facture'],
                'date_echeance' => $data['date_echeance'] ?? null,
                'statut' => $data['statut'],
                'mode_paiement' => $data['mode_paiement'] ?? null,
                'date_paiement' => ($data['statut'] === 'payee' && !$facture->date_paiement) ? now()->toDateString() : $facture->date_paiement,
                'appliquer_tva' => $appliquerTva,
                'taux_tva' => $tauxTva,
                'total_ht' => $totalHt,
                'montant_tva' => $montantTva,
                'total_ttc' => $totalTtc,
                'conditions_reglement' => $data['conditions_reglement'] ?? null,
                'notes' => $data['notes'] ?? null,
            ]);

            // Remplacer les lignes
            $facture->lignes()->delete();
            foreach ($data['lignes'] as $ligne) {
                $qte = (float) $ligne['quantite'];
                $pu = (float) $ligne['prix_unitaire'];
                FactureLigne::create([
                    'facture_id' => $facture->id,
                    'designation' => $ligne['designation'],
                    'quantite' => $qte,
                    'prix_unitaire' => $pu,
                    'total_ligne' => round($qte * $pu, 2),
                ]);
            }

            DB::commit();

            activity()->performedOn($facture)
                ->causedBy($request->user())
                ->log('Facture mise à jour');

            return redirect()->route('factures.show', $facture)->with('success', "Facture {$facture->numero} mise à jour.");
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Erreur lors de la mise à jour : ' . $e->getMessage());
        }
    }

    public function marquerPayee(Request $request, Facture $facture)
    {
        $data = $request->validate([
            'mode_paiement' => ['required', 'in:virement,cheque,especes,wave,orange_money'],
            'date_paiement' => ['required', 'date'],
            'reference_paiement' => ['nullable', 'string', 'max:100'],
        ]);

        $facture->update([
            'statut' => 'payee',
            'mode_paiement' => $data['mode_paiement'],
            'date_paiement' => $data['date_paiement'],
            'reference_paiement' => $data['reference_paiement'] ?? null,
        ]);

        activity()->performedOn($facture)
            ->causedBy($request->user())
            ->log('Facture marquée comme payée');

        return back()->with('success', "La facture {$facture->numero} est marquée comme payée.");
    }

    public function changerStatut(Request $request, Facture $facture)
    {
        $data = $request->validate([
            'statut' => ['required', 'in:brouillon,emise,annulee'],
        ]);

        $facture->update(['statut' => $data['statut']]);

        activity()->performedOn($facture)
            ->causedBy($request->user())
            ->withProperties(['nouveau_statut' => $data['statut']])
            ->log('Statut facture modifié');

        return back()->with('success', "Le statut de la facture {$facture->numero} a été mis à jour.");
    }

    public function destroy(Request $request, Facture $facture)
    {
        activity()->performedOn($facture)
            ->causedBy($request->user())
            ->withProperties(['numero' => $facture->numero])
            ->log('Facture supprimée');

        $facture->delete();

        return redirect()->route('factures.index')->with('success', 'Facture supprimée.');
    }

    public function pdf(Facture $facture)
    {
        $facture->load(['client', 'lignes', 'createur']);
        $param = Parametre::instance();

        $pdf = Pdf::loadView('exports.facture-pdf', compact('facture', 'param'));
        return $pdf->download("facture-{$facture->numero}.pdf");
    }
}
