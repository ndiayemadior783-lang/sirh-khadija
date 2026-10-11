<x-app-layout>
    <x-slot name="title">Émployés</x-slot>
    <x-slot name="header">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-2xl font-bold text-slate-800">Gestion des employés</h1>
                <p class="mt-1 text-sm text-slate-500">{{ $employes->total() }} employé(s) enregistré(s)</p>
            </div>
            <div class="flex gap-2">
                <button onclick="window.print()" class="btn-secondary">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4H7v4a2 2 0 002 2z"/></svg>
                    Imprimer
                </button>
                <a href="{{ route("employes.create") }}" class="btn-primary">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    Nouvel employé
                </a>
            </div>
                        <a href="{{ route('export.employes') }}" class="btn-primary" target="_blank"><svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg> Export PDF</a>
                <a href="{{ route('export.employes-csv') }}" class="btn-secondary" target="_blank"><svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg> Excel</a>
            </div>
    </x-slot>

<!-- Filtres -->
    <form method="GET" action="{{ route('employes.index') }}" class="card mb-6 p-4">
        <div class="grid grid-cols-1 gap-4 md:grid-cols-4">
            <div>
                <label class="mb-1 block text-xs font-semibold text-slate-600">Recherche</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Nom, prÃ©nom, matricule..."
                    class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-indigo-500 focus:ring-indigo-500">
            </div>
            <div>
                <label class="mb-1 block text-xs font-semibold text-slate-600">DÃ©partement</label>
                <select name="departement_id" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                    <option value="">Tous</option>
                    @foreach($departements as $dep)
                        <option value="{{ $dep->id }}" @selected(request('departement_id') == $dep->id)>{{ $dep->nom }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="mb-1 block text-xs font-semibold text-slate-600">Statut</label>
                <select name="statut" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                    <option value="">Tous</option>
                    <option value="actif" @selected(request('statut') == 'actif')>Actif</option>
                    <option value="inactif" @selected(request('statut') == 'inactif')>Inactif</option>
                    <option value="suspendu" @selected(request('statut') == 'suspendu')>Suspendu</option>
                    <option value="demission" @selected(request('statut') == 'demission')>DÃ©mission</option>
                </select>
            </div>
            <div class="flex items-end">
                <button type="submit" class="btn-secondary w-full">Filtrer</button>
            </div>
        </div>
    </form>

    <!-- Liste -->
    <div class="card overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">EmployÃ©</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">Matricule</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">Poste / DÃ©partement</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">Embauche</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">Salaire</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">Statut</th>
                        <th class="px-6 py-3 text-right text-xs font-semibold uppercase tracking-wider text-slate-500">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 bg-white">
                    @forelse($employes as $employe)
                        <tr class="hover:bg-slate-50">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-indigo-100 text-sm font-bold text-indigo-700">{{ $employe->initiales }}</div>
                                    <a href="{{ route('employes.show', $employe) }}" class="font-semibold text-slate-800 hover:text-indigo-600">{{ $employe->nomComplet }}</a>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-sm text-slate-600">{{ $employe->matricule }}</td>
                            <td class="px-6 py-4">
                                <p class="text-sm font-medium text-slate-700">{{ $employe->poste?->libelle ?? 'â€”' }}</p>
                                <p class="text-xs text-slate-500">{{ $employe->departement?->nom ?? 'â€”' }}</p>
                            </td>
                            <td class="px-6 py-4 text-sm text-slate-600">{{ $employe->date_embauche->format('d/m/Y') }}</td>
                            <td class="px-6 py-4 text-sm font-medium text-slate-700">{{ number_format($employe->salaire_base, 0, ',', ' ') }} F</td>
                            <td class="px-6 py-4">
                                <span class="badge {{ $employe->statut === 'actif' ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-100 text-slate-600' }}">{{ $employe->statut }}</span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <a href="{{ route('employes.show', $employe) }}" class="text-indigo-600 hover:text-indigo-800">
                                    <svg class="h-5 w-5 inline" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0zM2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-10 text-center text-sm text-slate-500">Aucun employÃ© trouvÃ©.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="border-t border-slate-100 px-6 py-4">
            {{ $employes->links() }}
        </div>
    </div>

    


    <style>
        @media print {
            aside, header, nav { display: none !important; }
            main { padding: 0 !important; max-width: 100% !important; }
            body { background: white !important; }
            .card { box-shadow: none !important; border: 1px solid #e2e8f0 !important; }
            table { font-size: 11px !important; }
            th, td { padding: 4px 8px !important; }
        }
    </style>
</x-app-layout>
