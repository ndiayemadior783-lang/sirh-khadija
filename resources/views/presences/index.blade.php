<x-app-layout>
    <x-slot name="title">Présences</x-slot>
    <x-slot name="header">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-2xl font-bold text-slate-800">Présences & Pointage</h1>
                <p class="mt-1 text-sm text-slate-500">Feuille de présence du personnel</p>
            </div>
            <a href="{{ route('presences.create') }}" class="btn-primary">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Pointer un employé
            </a>
        </div>
    </x-slot>

    <!-- Statistiques du jour -->
    <div class="grid grid-cols-2 gap-4 lg:grid-cols-4">
        <div class="stat-card">
            <p class="text-sm font-medium text-slate-500">Pointages</p>
            <p class="mt-1 text-2xl font-bold text-slate-800">{{ $presences->count() }}</p>
        </div>
        <div class="stat-card">
            <p class="text-sm font-medium text-slate-500">Présents</p>
            <p class="mt-1 text-2xl font-bold text-emerald-600">{{ $present }}</p>
        </div>
        <div class="stat-card">
            <p class="text-sm font-medium text-slate-500">Absents</p>
            <p class="mt-1 text-2xl font-bold text-rose-600">{{ $absent }}</p>
        </div>
        <div class="stat-card">
            <p class="text-sm font-medium text-slate-500">Taux de présence</p>
            <p class="mt-1 text-2xl font-bold text-indigo-600">{{ $taux }}%</p>
        </div>
    </div>

    <!-- Sélecteur de date -->
    <form method="GET" action="{{ route('presences.index') }}" class="card my-6 p-4">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center">
            <label class="text-sm font-semibold text-slate-600">Date :</label>
            <input type="date" name="date" value="{{ $date }}" class="rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-indigo-500 focus:ring-indigo-500">
            <button type="submit" class="btn-secondary">Afficher</button>
        </div>
    </form>

    <div class="card overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">Employé</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">Arrivée</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">Départ</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">Heures sup.</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">Statut</th>
                        <th class="px-6 py-3 text-right text-xs font-semibold uppercase tracking-wider text-slate-500">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 bg-white">
                    @forelse($presences as $presence)
                        <tr class="hover:bg-slate-50">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="flex h-9 w-9 items-center justify-center rounded-full bg-indigo-100 text-xs font-bold text-indigo-700">{{ $presence->employee?->initiales }}</div>
                                    <a href="{{ route('employes.show', $presence->employee) }}" class="text-sm font-semibold text-slate-800 hover:text-indigo-600">{{ $presence->employee?->nomComplet }}</a>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-sm text-slate-600">{{ $presence->heure_arrivee ?? '—' }}</td>
                            <td class="px-6 py-4 text-sm text-slate-600">{{ $presence->heure_depart ?? '—' }}</td>
                            <td class="px-6 py-4 text-sm text-slate-600">{{ $presence->heures_sup > 0 ? $presence->heures_sup . 'h' : '—' }}</td>
                            <td class="px-6 py-4">
                                <span class="badge
                                    @if($presence->statut === 'present') bg-emerald-100 text-emerald-700
                                    @elseif($presence->statut === 'absent') bg-rose-100 text-rose-700
                                    @else bg-amber-100 text-amber-700 @endif">
                                    {{ $presence->statut }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <form method="POST" action="{{ route('presences.destroy', $presence) }}" onsubmit="return confirm('Supprimer ce pointage ?')" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button class="text-slate-400 hover:text-rose-600">
                                        <svg class="h-5 w-5 inline" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="px-6 py-10 text-center text-sm text-slate-500">Aucun pointage pour cette date.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>