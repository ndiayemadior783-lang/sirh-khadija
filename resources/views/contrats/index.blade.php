<x-app-layout>
    <x-slot name="title">Contrats</x-slot>
    <x-slot name="header">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-2xl font-bold text-slate-800">Gestion des contrats</h1>
                <p class="mt-1 text-sm text-slate-500">Suivi des contrats de travail du personnel</p>
            </div>
            <a href="{{ route('contrats.create') }}" class="btn-primary">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Nouveau contrat
            </a>
                        <button onclick="window.print()" class="btn-secondary"><svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4H7v4a2 2 0 002 2z"/></svg> Imprimer</button>
</div>
    </x-slot>

    <form method="GET" action="{{ route('contrats.index') }}" class="card mb-6 p-4">
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
            <div>
                <label class="mb-1 block text-xs font-semibold text-slate-600">Type</label>
                <select name="type" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                    <option value="">Tous</option>
                    <option value="CDI" @selected(request('type') == 'CDI')>CDI</option>
                    <option value="CDD" @selected(request('type') == 'CDD')>CDD</option>
                    <option value="stage" @selected(request('type') == 'stage')>Stage</option>
                    <option value="freelance" @selected(request('type') == 'freelance')>Freelance</option>
                </select>
            </div>
            <div>
                <label class="mb-1 block text-xs font-semibold text-slate-600">Statut</label>
                <select name="statut" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                    <option value="">Tous</option>
                    <option value="en_cours" @selected(request('statut') == 'en_cours')>En cours</option>
                    <option value="termine" @selected(request('statut') == 'termine')>Terminé</option>
                </select>
            </div>
            <div class="flex items-end">
                <button type="submit" class="btn-secondary w-full">Filtrer</button>
            </div>
        </div>
    </form>

    <div class="card overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">Employé</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">Type</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">Début</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">Fin</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">Salaire</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">Statut</th>
                        <th class="px-6 py-3 text-right text-xs font-semibold uppercase tracking-wider text-slate-500">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 bg-white">
                    @forelse($contrats as $contrat)
                        <tr class="hover:bg-slate-50">
                            <td class="px-6 py-4">
                                <a href="{{ route('employes.show', $contrat->employee) }}" class="font-semibold text-slate-800 hover:text-indigo-600">{{ $contrat->employee?->nomComplet }}</a>
                            </td>
                            <td class="px-6 py-4"><span class="badge bg-indigo-100 text-indigo-700">{{ $contrat->libelleType() }}</span></td>
                            <td class="px-6 py-4 text-sm text-slate-600">{{ $contrat->date_debut?->format('d/m/Y') }}</td>
                            <td class="px-6 py-4 text-sm text-slate-600">{{ $contrat->date_fin?->format('d/m/Y') ?? '—' }}</td>
                            <td class="px-6 py-4 text-sm font-medium text-slate-700">{{ number_format($contrat->salaire_base, 0, ',', ' ') }} F</td>
                            <td class="px-6 py-4">
                                <span class="badge {{ $contrat->statut === 'en_cours' ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-100 text-slate-600' }}">{{ $contrat->statut }}</span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <a href="{{ route('contrats.show', $contrat) }}" class="text-indigo-600 hover:text-indigo-800">
                                    <svg class="h-5 w-5 inline" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0zM2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="px-6 py-10 text-center text-sm text-slate-500">Aucun contrat trouvé.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="border-t border-slate-100 px-6 py-4">{{ $contrats->links() }}</div>
    </div>
</x-app-layout>