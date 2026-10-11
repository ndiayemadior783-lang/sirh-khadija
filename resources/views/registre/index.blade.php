<x-app-layout>
    <x-slot name="title">Registre unique du personnel</x-slot>
    <x-slot name="header">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-2xl font-bold text-slate-800">Registre unique du personnel (RUP)</h1>
                <p class="mt-1 text-sm text-slate-500">Déclaration de la main-d’œuvre — {{ $param->nom_entreprise }}</p>
            </div>
            <div class="flex flex-wrap gap-2">
                <button onclick="window.print()" class="btn-secondary">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4H7v4a2 2 0 002 2z"/></svg>
                    Imprimer
                </button>
                <a href="{{ route('registre.pdf') }}" target="_blank" class="btn-primary">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    Export PDF
                </a>
                <a href="{{ route('registre.csv') }}" target="_blank" class="btn-secondary">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    Excel / CSV
                </a>
            </div>
        </div>
    </x-slot>

    <!-- En-tête entreprise -->
    <div class="card mb-6 p-6">
        <div class="grid grid-cols-2 gap-4 lg:grid-cols-5">
            <div>
                <p class="text-xs font-medium text-slate-500">Raison sociale</p>
                <p class="mt-1 text-sm font-bold text-slate-800">{{ $param->nom_entreprise }}</p>
            </div>
            <div>
                <p class="text-xs font-medium text-slate-500">Adresse</p>
                <p class="mt-1 text-sm text-slate-700">{{ $param->adresse ?? '—' }}</p>
            </div>
            <div>
                <p class="text-xs font-medium text-slate-500">NINEA</p>
                <p class="mt-1 text-sm text-slate-700">{{ $param->nine_a ?? '—' }}</p>
            </div>
            <div>
                <p class="text-xs font-medium text-slate-500">RCCM</p>
                <p class="mt-1 text-sm text-slate-700">{{ $param->rc_cm ?? '—' }}</p>
            </div>
            <div>
                <p class="text-xs font-medium text-slate-500">Effectif</p>
                <p class="mt-1 text-sm font-bold text-indigo-600">{{ $employes->count() }} employé(s)</p>
            </div>
        </div>
    </div>

    <!-- Tableau du registre -->
    <div class="card overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">N°</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">Prénoms et Nom</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">Date naiss.</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">Lieu naiss.</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">Sexe</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">Nationalité</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">N° CNI</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">Embauche</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">Emploi</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">Salaire</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">Adresse</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">Statut</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 bg-white">
                    @forelse($employes as $i => $e)
                        <tr class="hover:bg-slate-50">
                            <td class="px-4 py-3 text-sm text-slate-500">{{ $i + 1 }}</td>
                            <td class="px-4 py-3 text-sm font-semibold text-slate-800">{{ $e->nomComplet }}</td>
                            <td class="px-4 py-3 text-sm text-slate-600">{{ $e->date_naissance?->format('d/m/Y') ?? '—' }}</td>
                            <td class="px-4 py-3 text-sm text-slate-600">{{ $e->lieu_naissance ?? '—' }}</td>
                            <td class="px-4 py-3 text-sm text-slate-600">{{ $e->genre === 'M' ? 'M' : 'F' }}</td>
                            <td class="px-4 py-3 text-sm text-slate-600">{{ $e->nationalite ?? '—' }}</td>
                            <td class="px-4 py-3 text-sm text-slate-600">{{ $e->cin ?? '—' }}</td>
                            <td class="px-4 py-3 text-sm text-slate-600">{{ $e->date_embauche?->format('d/m/Y') }}</td>
                            <td class="px-4 py-3 text-sm text-slate-700">{{ $e->poste?->libelle ?? '—' }}</td>
                            <td class="px-4 py-3 text-sm font-medium text-slate-700">{{ number_format($e->salaire_base, 0, ',', ' ') }} F</td>
                            <td class="px-4 py-3 text-sm text-slate-600">{{ trim(($e->adresse ?? '') . ' ' . ($e->ville ?? '')) ?: '—' }}</td>
                            <td class="px-4 py-3">
                                <span class="badge {{ $e->statut === 'actif' ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-100 text-slate-600' }}">{{ $e->statut }}</span>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="12" class="px-6 py-10 text-center text-sm text-slate-500">Aucun employé enregistré pour le moment.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <style>
        @media print {
            aside, nav, .btn-secondary, .btn-primary { display: none !important; }
            main { padding: 0 !important; max-width: 100% !important; }
            body { background: white !important; }
            .card { box-shadow: none !important; border: 1px solid #e2e8f0 !important; }
            table { font-size: 10px !important; }
            th, td { padding: 3px 6px !important; }
        }
    </style>
</x-app-layout>