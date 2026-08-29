@php $user = auth()->user(); @endphp
<x-app-layout>
    <x-slot name="title">Congés & Absences</x-slot>
    <x-slot name="header">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-2xl font-bold text-slate-800">Gestion des congés & absences</h1>
                <p class="mt-1 text-sm text-slate-500">Demandes de congés et suivi des validations</p>
            </div>
            <a href="{{ route('conges.create') }}" class="btn-primary">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Nouvelle demande
            </a>
        </div>
    </x-slot>

    <!-- Statistiques -->
    <div class="grid grid-cols-2 gap-4 lg:grid-cols-4">
        <div class="stat-card"><p class="text-sm font-medium text-slate-500">Total demandes</p><p class="mt-1 text-2xl font-bold text-slate-800">{{ $conges->total() }}</p></div>
        <div class="stat-card"><p class="text-sm font-medium text-slate-500">En attente</p><p class="mt-1 text-2xl font-bold text-amber-600">{{ $enAttente }}</p></div>
        <div class="stat-card"><p class="text-sm font-medium text-slate-500">Validés</p><p class="mt-1 text-2xl font-bold text-emerald-600">{{ $conges->where('statut', 'valide')->count() }}</p></div>
        <div class="stat-card"><p class="text-sm font-medium text-slate-500">Refusés</p><p class="mt-1 text-2xl font-bold text-rose-600">{{ $conges->where('statut', 'refuse')->count() }}</p></div>
    </div>

    <div class="card mt-6 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">Employé</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">Type</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">Période</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">Jours</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">Motif</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">Statut</th>
                        @if($user->estGestion())
                        <th class="px-6 py-3 text-right text-xs font-semibold uppercase tracking-wider text-slate-500">Actions</th>
                        @endif
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 bg-white">
                    @forelse($conges as $conge)
                        <tr class="hover:bg-slate-50">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="flex h-9 w-9 items-center justify-center rounded-full bg-indigo-100 text-xs font-bold text-indigo-700">{{ $conge->employee?->initiales }}</div>
                                    <span class="text-sm font-semibold text-slate-800">{{ $conge->employee?->nomComplet }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-sm text-slate-600">{{ $conge->libelleType() }}</td>
                            <td class="px-6 py-4 text-sm text-slate-600">{{ $conge->date_debut?->format('d/m/Y') }} → {{ $conge->date_fin?->format('d/m/Y') }}</td>
                            <td class="px-6 py-4 text-sm font-semibold text-slate-700">{{ $conge->jours }}</td>
                            <td class="px-6 py-4 text-sm text-slate-600 max-w-[200px] truncate">{{ $conge->motif ?? '—' }}</td>
                            <td class="px-6 py-4">
                                <span class="badge
                                    @if($conge->statut === 'valide') bg-emerald-100 text-emerald-700
                                    @elseif($conge->statut === 'refuse') bg-rose-100 text-rose-700
                                    @else bg-amber-100 text-amber-700 @endif">{{ $conge->statut }}</span>
                            </td>
                            @if($user->estGestion() && $conge->statut === 'en_attente')
                            <td class="px-6 py-4">
                                <div class="flex justify-end gap-2">
                                    <form method="POST" action="{{ route('conges.valider', $conge) }}">
                                        @csrf
                                        <button class="inline-flex items-center gap-1 rounded-lg bg-emerald-600 px-3 py-1.5 text-xs font-semibold text-white hover:bg-emerald-700">
                                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                            Valider
                                        </button>
                                    </form>
                                    <form method="POST" action="{{ route('conges.rejeter', $conge) }}">
                                        @csrf
                                        <button class="inline-flex items-center gap-1 rounded-lg bg-rose-600 px-3 py-1.5 text-xs font-semibold text-white hover:bg-rose-700">
                                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                            Refuser
                                        </button>
                                    </form>
                                </div>
                            </td>
                            @elseif($user->estGestion())
                            <td class="px-6 py-4"></td>
                            @endif
                        </tr>
                    @empty
                        <tr><td colspan="7" class="px-6 py-10 text-center text-sm text-slate-500">Aucune demande de congé.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="border-t border-slate-100 px-6 py-4">{{ $conges->links() }}</div>
    </div>
</x-app-layout>