<x-app-layout>
    <x-slot name="title">Factures</x-slot>
    <x-slot name="header">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-2xl font-bold text-slate-800">Gestion de la facturation</h1>
                <p class="mt-1 text-sm text-slate-500">Missions de conseil, honoraires & prestations de Khadija Consulting</p>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('clients.index') }}" class="btn-secondary">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a4 4 0 00-3-3.87M9 20H4v-2a4 4 0 013-3.87m6-1.13a4 4 0 10-4-4 4 4 0 004 4zm6 1.13a3 3 0 10-3-3 3 3 0 003 3zM9 12a4 4 0 01-4-4v-2a4 4 0 018 0v2a4 4 0 01-4 4z"/></svg>
                    Gérer les clients
                </a>
                <a href="{{ route('factures.create') }}" class="btn-primary">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    Nouvelle facture
                </a>
            </div>
        </div>
    </x-slot>

    <!-- Cartes KPI / Chiffres clés -->
    <div class="mb-6 grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
        <div class="card p-5 border-l-4 border-indigo-500">
            <div class="text-xs font-semibold uppercase tracking-wider text-slate-500">Total Facturé (TTC)</div>
            <div class="mt-2 text-2xl font-bold text-slate-900">{{ number_format($totalFacture, 0, ',', ' ') }} <span class="text-sm font-normal text-slate-500">FCFA</span></div>
            <div class="mt-1 text-xs text-slate-400">Toutes factures actives</div>
        </div>

        <div class="card p-5 border-l-4 border-emerald-500">
            <div class="text-xs font-semibold uppercase tracking-wider text-slate-500">Encaissé / Payé</div>
            <div class="mt-2 text-2xl font-bold text-emerald-700">{{ number_format($totalPaye, 0, ',', ' ') }} <span class="text-sm font-normal text-slate-500">FCFA</span></div>
            <div class="mt-1 text-xs text-emerald-600 font-medium">Fonds reçus</div>
        </div>

        <div class="card p-5 border-l-4 border-sky-500">
            <div class="text-xs font-semibold uppercase tracking-wider text-slate-500">En attente de règlement</div>
            <div class="mt-2 text-2xl font-bold text-sky-700">{{ number_format($totalEnAttente, 0, ',', ' ') }} <span class="text-sm font-normal text-slate-500">FCFA</span></div>
            <div class="mt-1 text-xs text-sky-600 font-medium">Factures émises</div>
        </div>

        <div class="card p-5 border-l-4 border-amber-500">
            <div class="text-xs font-semibold uppercase tracking-wider text-slate-500">Brouillons en cours</div>
            <div class="mt-2 text-2xl font-bold text-amber-700">{{ number_format($totalBrouillon, 0, ',', ' ') }} <span class="text-sm font-normal text-slate-500">FCFA</span></div>
            <div class="mt-1 text-xs text-slate-400">Non encore émises</div>
        </div>
    </div>

    <!-- Filtres -->
    <form method="GET" action="{{ route('factures.index') }}" class="card mb-6 p-4">
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-4">
            <div>
                <label class="mb-1 block text-xs font-semibold text-slate-600">Recherche</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="N° facture ou client..."
                       class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-indigo-500 focus:ring-indigo-500">
            </div>

            <div>
                <label class="mb-1 block text-xs font-semibold text-slate-600">Client</label>
                <select name="client_id" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                    <option value="">Tous les clients</option>
                    @foreach($clients as $c)
                        <option value="{{ $c->id }}" @selected(request('client_id') == $c->id)>{{ $c->nom }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="mb-1 block text-xs font-semibold text-slate-600">Statut</label>
                <select name="statut" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                    <option value="">Tous les statuts</option>
                    <option value="brouillon" @selected(request('statut') == 'brouillon')>Brouillon</option>
                    <option value="emise" @selected(request('statut') == 'emise')>Émise</option>
                    <option value="payee" @selected(request('statut') == 'payee')>Payée</option>
                    <option value="annulee" @selected(request('statut') == 'annulee')>Annulée</option>
                </select>
            </div>

            <div class="flex items-end gap-2">
                <button type="submit" class="btn-secondary w-full">Filtrer</button>
                @if(request()->anyFilled(['search', 'client_id', 'statut']))
                    <a href="{{ route('factures.index') }}" class="btn-secondary">Effacer</a>
                @endif
            </div>
        </div>
    </form>

    <!-- Tableau des factures -->
    <div class="card overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">Numéro</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">Client</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">Date émission</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">Échéance</th>
                        <th class="px-6 py-3 text-right text-xs font-semibold uppercase tracking-wider text-slate-500">Montant HT</th>
                        <th class="px-6 py-3 text-right text-xs font-semibold uppercase tracking-wider text-slate-500">Montant TTC</th>
                        <th class="px-6 py-3 text-center text-xs font-semibold uppercase tracking-wider text-slate-500">Statut</th>
                        <th class="px-6 py-3 text-right text-xs font-semibold uppercase tracking-wider text-slate-500">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 bg-white">
                    @forelse($factures as $facture)
                        <tr class="hover:bg-slate-50">
                            <td class="px-6 py-4">
                                <a href="{{ route('factures.show', $facture) }}" class="font-mono text-sm font-bold text-indigo-600 hover:text-indigo-900">
                                    {{ $facture->numero }}
                                </a>
                            </td>
                            <td class="px-6 py-4">
                                <div class="font-semibold text-slate-900">{{ $facture->client?->nom ?? '—' }}</div>
                                <div class="text-xs text-slate-500">{{ $facture->client?->contact_nom }}</div>
                            </td>
                            <td class="px-6 py-4 text-sm text-slate-700">
                                {{ $facture->date_facture?->format('d/m/Y') }}
                            </td>
                            <td class="px-6 py-4 text-sm text-slate-700">
                                {{ $facture->date_echeance ? $facture->date_echeance->format('d/m/Y') : '—' }}
                            </td>
                            <td class="px-6 py-4 text-right font-mono text-sm text-slate-600">
                                {{ number_format($facture->total_ht, 0, ',', ' ') }} FCFA
                            </td>
                            <td class="px-6 py-4 text-right font-mono text-sm font-bold text-slate-900">
                                {{ number_format($facture->total_ttc, 0, ',', ' ') }} FCFA
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="inline-flex rounded-full border px-2.5 py-0.5 text-xs font-semibold {{ $facture->badgeClasses() }}">
                                    {{ $facture->libelleStatut() }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('factures.pdf', $facture) }}" class="rounded p-1 text-slate-500 hover:bg-slate-100 hover:text-indigo-600" title="Télécharger PDF">
                                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                    </a>
                                    <a href="{{ route('factures.show', $facture) }}" class="rounded p-1 text-slate-500 hover:bg-slate-100 hover:text-slate-800" title="Consulter">
                                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                    </a>
                                    <a href="{{ route('factures.edit', $facture) }}" class="rounded p-1 text-slate-500 hover:bg-slate-100 hover:text-indigo-600" title="Modifier">
                                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                    </a>
                                    <form method="POST" action="{{ route('factures.destroy', $facture) }}" onsubmit="return confirm('Supprimer cette facture ?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="rounded p-1 text-slate-500 hover:bg-slate-100 hover:text-rose-600" title="Supprimer">
                                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-8 text-center text-sm text-slate-500">
                                Aucune facture enregistrée pour le moment.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($factures->hasPages())
            <div class="border-t border-slate-200 p-4">
                {{ $factures->links() }}
            </div>
        @endif
    </div>
</x-app-layout>
