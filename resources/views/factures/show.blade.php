<x-app-layout>
    <x-slot name="title">Facture {{ $facture->numero }}</x-slot>
    <x-slot name="header">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div class="flex items-center gap-3">
                <a href="{{ route('factures.index') }}" class="rounded-lg p-1 text-slate-500 hover:bg-slate-100 hover:text-slate-800">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                </a>
                <div>
                    <div class="flex items-center gap-3">
                        <h1 class="text-2xl font-bold font-mono text-slate-900">{{ $facture->numero }}</h1>
                        <span class="inline-flex rounded-full border px-2.5 py-0.5 text-xs font-semibold {{ $facture->badgeClasses() }}">
                            {{ $facture->libelleStatut() }}
                        </span>
                    </div>
                    <p class="text-xs text-slate-500">Client : {{ $facture->client?->nom }} • Émise le {{ $facture->date_facture?->format('d/m/Y') }}</p>
                </div>
            </div>

            <div class="flex flex-wrap items-center gap-2">
                <a href="{{ route('factures.pdf', $facture) }}" class="btn-primary">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    Télécharger PDF
                </a>

                <button onclick="window.print()" class="btn-secondary">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4H7v4a2 2 0 002 2z"/></svg>
                    Imprimer
                </button>

                <a href="{{ route('factures.edit', $facture) }}" class="btn-secondary">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                    Modifier
                </a>
            </div>
        </div>
    </x-slot>

    <!-- Zone de paiement rapide si non payée -->
    @if($facture->statut !== 'payee')
        <div class="mb-6 card p-4 bg-emerald-50/50 border-emerald-200">
            <form method="POST" action="{{ route('factures.marquer-payee', $facture) }}" class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                @csrf
                <div>
                    <h3 class="text-sm font-bold text-emerald-900">Enregistrer le règlement de cette facture</h3>
                    <p class="text-xs text-emerald-700">Valider l'encaissement des fonds pour solder le document.</p>
                </div>
                <div class="flex flex-wrap items-center gap-2">
                    <select name="mode_paiement" required class="rounded-lg border border-emerald-300 bg-white px-3 py-1.5 text-xs text-slate-800">
                        <option value="virement" @selected($facture->mode_paiement == 'virement')>Virement bancaire</option>
                        <option value="cheque" @selected($facture->mode_paiement == 'cheque')>Chèque</option>
                        <option value="especes" @selected($facture->mode_paiement == 'especes')>Espèces</option>
                        <option value="wave" @selected($facture->mode_paiement == 'wave')>Wave</option>
                        <option value="orange_money" @selected($facture->mode_paiement == 'orange_money')>Orange Money</option>
                    </select>
                    <input type="date" name="date_paiement" value="{{ now()->toDateString() }}" required
                           class="rounded-lg border border-emerald-300 bg-white px-3 py-1.5 text-xs text-slate-800">
                    <input type="text" name="reference_paiement" placeholder="N° chèque ou virement..."
                           class="rounded-lg border border-emerald-300 bg-white px-3 py-1.5 text-xs text-slate-800">
                    <button type="submit" class="rounded-lg bg-emerald-600 px-3 py-1.5 text-xs font-semibold text-white hover:bg-emerald-700">
                        Valider paiement
                    </button>
                </div>
            </form>
        </div>
    @endif

    <!-- Document Facture (Format Papier / Écran) -->
    <div class="card p-8 sm:p-12 max-w-4xl mx-auto bg-white shadow-sm border border-slate-200">
        <!-- En-tête -->
        <div class="flex flex-col sm:flex-row justify-between items-start gap-8 border-b border-slate-200 pb-8">
            <div>
                <div class="flex items-center gap-3">
                    <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-indigo-600 text-2xl font-black text-white">
                        K
                    </div>
                    <div>
                        <h2 class="text-xl font-bold text-slate-900">{{ $param->nom_entreprise ?: 'KHADIJA CONSULTING' }}</h2>
                        <p class="text-xs text-slate-500 font-medium">Cabinet de Conseil & Solutions RH</p>
                    </div>
                </div>
                <div class="mt-4 text-xs text-slate-600 space-y-1">
                    <p>{{ $param->adresse ?: 'Dakar, Sénégal' }}</p>
                    @if($param->telephone)<p>Tél : {{ $param->telephone }}</p>@endif
                    @if($param->email)<p>Email : {{ $param->email }}</p>@endif
                    @if($param->nine_a)<p class="font-mono">NINEA : {{ $param->nine_a }}</p>@endif
                    @if($param->rc_cm)<p class="font-mono">RC : {{ $param->rc_cm }}</p>@endif
                </div>
            </div>

            <!-- Bloc Informations Facture -->
            <div class="sm:text-right">
                <div class="inline-block rounded-lg bg-indigo-50 px-4 py-2 border border-indigo-100">
                    <div class="text-xs uppercase tracking-wider font-semibold text-indigo-700">FACTURE</div>
                    <div class="font-mono text-xl font-black text-indigo-950">{{ $facture->numero }}</div>
                </div>
                <div class="mt-4 text-xs text-slate-600 space-y-1">
                    <p><span class="font-semibold text-slate-700">Date d'émission :</span> {{ $facture->date_facture?->format('d/m/Y') }}</p>
                    @if($facture->date_echeance)
                        <p><span class="font-semibold text-slate-700">Date d'échéance :</span> {{ $facture->date_echeance->format('d/m/Y') }}</p>
                    @endif
                    @if($facture->date_paiement)
                        <p class="text-emerald-700 font-semibold">Payée le : {{ $facture->date_paiement->format('d/m/Y') }}</p>
                    @endif
                    <p><span class="font-semibold text-slate-700">Règlement :</span> {{ $facture->libelleModePaiement() }}</p>
                </div>
            </div>
        </div>

        <!-- Coordonnées Client -->
        <div class="my-8 rounded-lg bg-slate-50 p-6 border border-slate-200">
            <div class="text-xs font-bold uppercase tracking-wider text-slate-400 mb-2">Facturé à :</div>
            <div class="text-lg font-bold text-slate-900">{{ $facture->client?->nom }}</div>
            <div class="mt-2 grid grid-cols-1 sm:grid-cols-2 gap-2 text-xs text-slate-600">
                <div>
                    @if($facture->client?->contact_nom)<p><span class="font-medium text-slate-500">À l'attention de :</span> {{ $facture->client->contact_nom }}</p>@endif
                    @if($facture->client?->adresse)<p>{{ $facture->client->adresse }}</p>@endif
                    <p>{{ $facture->client?->ville ?: 'Dakar, Sénégal' }}</p>
                </div>
                <div>
                    @if($facture->client?->telephone)<p>Tél : {{ $facture->client->telephone }}</p>@endif
                    @if($facture->client?->email)<p>Email : {{ $facture->client->email }}</p>@endif
                    @if($facture->client?->ninea)<p class="font-mono">NINEA : {{ $facture->client->ninea }}</p>@endif
                    @if($facture->client?->rc_cm)<p class="font-mono">RC : {{ $facture->client->rc_cm }}</p>@endif
                </div>
            </div>
        </div>

        <!-- Tableau des prestations -->
        <div class="overflow-x-auto my-6">
            <table class="min-w-full divide-y divide-slate-200 text-sm">
                <thead>
                    <tr class="border-b-2 border-slate-800">
                        <th class="py-3 text-left font-bold uppercase tracking-wider text-slate-800 text-xs">Description de la prestation</th>
                        <th class="py-3 px-4 text-center font-bold uppercase tracking-wider text-slate-800 text-xs">Quantité</th>
                        <th class="py-3 px-4 text-right font-bold uppercase tracking-wider text-slate-800 text-xs">Prix Unitaire</th>
                        <th class="py-3 text-right font-bold uppercase tracking-wider text-slate-800 text-xs">Montant HT</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($facture->lignes as $ligne)
                        <tr>
                            <td class="py-4 text-slate-800 font-medium">{{ $ligne->designation }}</td>
                            <td class="py-4 px-4 text-center text-slate-600 font-mono">{{ number_format($ligne->quantite, 1, ',', '') }}</td>
                            <td class="py-4 px-4 text-right text-slate-600 font-mono">{{ number_format($ligne->prix_unitaire, 0, ',', ' ') }} FCFA</td>
                            <td class="py-4 text-right font-mono font-bold text-slate-900">{{ number_format($ligne->total_ligne, 0, ',', ' ') }} FCFA</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Totaux & Règlement -->
        <div class="flex flex-col sm:flex-row justify-between items-start gap-8 pt-6 border-t border-slate-200">
            <div class="w-full sm:w-1/2 text-xs text-slate-600 space-y-3">
                @if($facture->conditions_reglement)
                    <div>
                        <div class="font-bold text-slate-800 uppercase tracking-wider text-[11px]">Modalités de paiement :</div>
                        <p class="mt-1 leading-relaxed">{{ $facture->conditions_reglement }}</p>
                    </div>
                @endif
                @if($facture->notes)
                    <div>
                        <div class="font-bold text-slate-800 uppercase tracking-wider text-[11px]">Remarques :</div>
                        <p class="mt-1 leading-relaxed">{{ $facture->notes }}</p>
                    </div>
                @endif
            </div>

            <div class="w-full sm:w-72 space-y-2">
                <div class="flex justify-between text-xs text-slate-600">
                    <span>Total Hors Taxes :</span>
                    <span class="font-mono font-semibold text-slate-900">{{ number_format($facture->total_ht, 0, ',', ' ') }} FCFA</span>
                </div>
                @if($facture->appliquer_tva)
                    <div class="flex justify-between text-xs text-slate-600">
                        <span>TVA ({{ number_format($facture->taux_tva, 0) }}%) :</span>
                        <span class="font-mono font-semibold text-slate-900">{{ number_format($facture->montant_tva, 0, ',', ' ') }} FCFA</span>
                    </div>
                @else
                    <div class="flex justify-between text-xs text-slate-500 italic">
                        <span>TVA :</span>
                        <span>Exonéré</span>
                    </div>
                @endif
                <div class="flex justify-between border-t-2 border-slate-900 pt-2 text-base font-bold text-slate-900">
                    <span>Net à payer :</span>
                    <span class="font-mono text-indigo-700">{{ number_format($facture->total_ttc, 0, ',', ' ') }} FCFA</span>
                </div>
            </div>
        </div>

        <!-- Pied de page -->
        <div class="mt-12 pt-6 border-t border-slate-200 text-center text-[11px] text-slate-400">
            <p>{{ $param->nom_entreprise ?: 'Khadija Consulting' }} — Dakar, Sénégal</p>
            <p class="mt-0.5">Facture générée automatiquement via le SIRH Khadija Consulting</p>
        </div>
    </div>
</x-app-layout>
