<x-app-layout>
    <x-slot name="header">
        <h1 class="text-2xl font-bold text-slate-800">Tableau de bord</h1>
        <p class="mt-1 text-sm text-slate-500">Vue d'ensemble du système d'information RH — {{ now()->translatedFormat('l d F Y') }}</p>
    </x-slot>

    <!-- Cartes statistiques -->
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
        <div class="stat-card">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-slate-500">Effectif total</p>
                    <p class="mt-1 text-3xl font-bold text-slate-800">{{ $totalEmployes }}</p>
                    <p class="mt-1 text-xs text-emerald-600">{{ $actifs }} actifs</p>
                </div>
                <div class="stat-icon bg-indigo-50 text-indigo-600">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a4 4 0 00-3-3.87M9 20H4v-2a4 4 0 013-3.87m6-1.13a4 4 0 10-4-4 4 4 0 004 4zm6 1.13a3 3 0 10-3-3 3 3 0 003 3z"/></svg>
                </div>
            </div>
        </div>
        <div class="stat-card">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-slate-500">Départements</p>
                    <p class="mt-1 text-3xl font-bold text-slate-800">{{ $departements }}</p>
                    <p class="mt-1 text-xs text-slate-400">services actifs</p>
                </div>
                <div class="stat-icon bg-emerald-50 text-emerald-600">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                </div>
            </div>
        </div>
        <div class="stat-card">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-slate-500">Congés en attente</p>
                    <p class="mt-1 text-3xl font-bold text-slate-800">{{ $congesEnAttente }}</p>
                    <p class="mt-1 text-xs text-amber-600">demandes à traiter</p>
                </div>
                <div class="stat-icon bg-amber-50 text-amber-600">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                </div>
            </div>
        </div>
        <div class="stat-card">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-slate-500">Masse salariale brute</p>
                    <p class="mt-1 text-3xl font-bold text-slate-800">{{ number_format($masseSalariale, 0, ',', ' ') }} F</p>
                    <p class="mt-1 text-xs text-slate-400">net payé ce mois : {{ number_format($bulletinCourant, 0, ',', ' ') }} F</p>
                </div>
                <div class="stat-icon bg-rose-50 text-rose-600">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Graphiques -->
    <div class="mt-6 grid grid-cols-1 gap-6 lg:grid-cols-2">
        <div class="card p-6">
            <h2 class="text-base font-semibold text-slate-800">Répartition par département</h2>
            <canvas id="chartDepartements" class="mt-4" height="220"></canvas>
        </div>
        <div class="card p-6">
            <h2 class="text-base font-semibold text-slate-800">Évolution de la masse salariale (6 mois)</h2>
            <canvas id="chartPaie" class="mt-4" height="220"></canvas>
        </div>
    </div>

    <!-- Activité récente -->
    <div class="mt-6 grid grid-cols-1 gap-6 lg:grid-cols-2">
        <div class="card overflow-hidden">
            <div class="border-b border-slate-100 px-6 py-4">
                <h2 class="text-base font-semibold text-slate-800">Derniers employés</h2>
            </div>
            <div class="divide-y divide-slate-100">
                @forelse($derniersEmployes as $emp)
                    <div class="flex items-center gap-3 px-6 py-3">
                        <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-indigo-100 text-sm font-bold text-indigo-700">
                            {{ $emp->initiales }}
                        </div>
                        <div class="min-w-0 flex-1">
                            <a href="{{ route('employes.show', $emp) }}" class="truncate text-sm font-semibold text-slate-800 hover:text-indigo-600">{{ $emp->nomComplet }}</a>
                            <p class="truncate text-xs text-slate-500">{{ $emp->poste?->libelle }} · {{ $emp->departement?->nom }}</p>
                        </div>
                        <span class="badge {{ $emp->statut === 'actif' ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-100 text-slate-600' }}">{{ $emp->statut }}</span>
                    </div>
                @empty
                    <p class="px-6 py-4 text-sm text-slate-500">Aucun employé.</p>
                @endforelse
            </div>
        </div>

        <div class="card overflow-hidden">
            <div class="border-b border-slate-100 px-6 py-4">
                <h2 class="text-base font-semibold text-slate-800">Dernières demandes de congés</h2>
            </div>
            <div class="divide-y divide-slate-100">
                @forelse($congesRecents as $conge)
                    <div class="flex items-center gap-3 px-6 py-3">
                        <div class="min-w-0 flex-1">
                            <p class="truncate text-sm font-semibold text-slate-800">{{ $conge->employee?->nomComplet }}</p>
                            <p class="truncate text-xs text-slate-500">{{ $conge->libelleType() }} · {{ $conge->date_debut->format('d/m/Y') }} → {{ $conge->date_fin->format('d/m/Y') }}</p>
                        </div>
                        <span class="badge
                            {{ $conge->statut === 'valide' ? 'bg-emerald-100 text-emerald-700' : ($conge->statut === 'refuse' ? 'bg-rose-100 text-rose-700' : 'bg-amber-100 text-amber-700') }}">
                            {{ $conge->statut }}
                        </span>
                    </div>
                @empty
                    <p class="px-6 py-4 text-sm text-slate-500">Aucune demande.</p>
                @endforelse
            </div>
        </div>
    </div>

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const depLabels = @json(array_keys($parDepartement->toArray()));
            const depData = @json(array_values($parDepartement->toArray()));

            new Chart(document.getElementById('chartDepartements'), {
                type: 'doughnut',
                data: {
                    labels: depLabels,
                    datasets: [{
                        data: depData,
                        backgroundColor: ['#6366f1', '#10b981', '#f59e0b', '#ef4444', '#0ea5e9', '#8b5cf6', '#14b8a6'],
                    }],
                },
                options: { plugins: { legend: { position: 'bottom' } } },
            });

            const paieLabels = @json(array_keys($evolutionPaie));
            const paieData = @json(array_values($evolutionPaie));

            new Chart(document.getElementById('chartPaie'), {
                type: 'line',
                data: {
                    labels: paieLabels,
                    datasets: [{
                        label: 'Net payé (FCFA)',
                        data: paieData,
                        borderColor: '#6366f1',
                        backgroundColor: 'rgba(99, 102, 241, 0.1)',
                        fill: true,
                        tension: 0.4,
                    }],
                },
                options: { plugins: { legend: { display: false } } },
            });
        });
    </script>
    @endpush
</x-app-layout>