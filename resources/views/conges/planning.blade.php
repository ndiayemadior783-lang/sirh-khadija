@php $user = auth()->user(); @endphp
<x-app-layout>
    <x-slot name="title">Planning des congés</x-slot>
    <x-slot name="header">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-2xl font-bold text-slate-800">Planning des congés — {{ $nomMois }} {{ $annee }}</h1>
                <p class="mt-1 text-sm text-slate-500">Vue mensuelle des congés et absences · {{ $societe }}</p>
            </div>
            <div class="flex flex-wrap gap-2">
                <a href="{{ route('conges.planning', ['mois' => $debut->copy()->subMonth()->month, 'annee' => $debut->copy()->subMonth()->year]) }}" class="btn-secondary">← Précédent</a>
                <a href="{{ route('conges.planning') }}" class="btn-secondary">Aujourd'hui</a>
                <a href="{{ route('conges.planning', ['mois' => $debut->copy()->addMonth()->month, 'annee' => $debut->copy()->addMonth()->year]) }}" class="btn-secondary">Suivant →</a>
                <a href="{{ route('conges.planning-pdf', ['mois' => $mois, 'annee' => $annee]) }}" target="_blank" class="btn-primary">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    Export PDF
                </a>
                <button onclick="window.print()" class="btn-secondary">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4H7v4a2 2 0 002 2z"/></svg>
                    Imprimer
                </button>
            </div>
        </div>
    </x-slot>

    <!-- Statistiques du mois -->
    <div class="grid grid-cols-2 gap-4 lg:grid-cols-3">
        <div class="stat-card"><p class="text-sm font-medium text-slate-500">Congés validés</p><p class="mt-1 text-2xl font-bold text-emerald-600">{{ $statsValides }}</p></div>
        <div class="stat-card"><p class="text-sm font-medium text-slate-500">En attente</p><p class="mt-1 text-2xl font-bold text-amber-600">{{ $statsAttente }}</p></div>
        <div class="stat-card"><p class="text-sm font-medium text-slate-500">Refusés</p><p class="mt-1 text-2xl font-bold text-rose-600">{{ $statsRefuses }}</p></div>
    </div>

    <!-- Calendrier -->
    <div class="card mt-6 overflow-hidden">
        <div class="flex flex-col gap-2 border-b border-slate-100 px-6 py-4 sm:flex-row sm:items-center sm:justify-between">
            <h2 class="text-base font-semibold text-slate-800">{{ $nomMois }} {{ $annee }}</h2>
            <div class="flex flex-wrap items-center gap-3 text-xs text-slate-600">
                <span class="inline-flex items-center gap-1"><span class="inline-block h-2.5 w-2.5 rounded-full bg-emerald-500"></span> Validé</span>
                <span class="inline-flex items-center gap-1"><span class="inline-block h-2.5 w-2.5 rounded-full bg-amber-500"></span> En attente</span>
                <span class="inline-flex items-center gap-1"><span class="inline-block h-2.5 w-2.5 rounded-full bg-rose-500"></span> Refusé</span>
                <span class="inline-flex items-center gap-1"><span class="inline-block h-2.5 w-2.5 rounded-full bg-indigo-500"></span> Aujourd'hui</span>
            </div>
        </div>
        <div class="p-4">
            @include('conges.partials.calendar-grid', ['compact' => false])
        </div>
    </div>

    <style>
        @media print {
            aside, nav, .btn-secondary, .btn-primary { display: none !important; }
            main { padding: 0 !important; max-width: 100% !important; }
            body { background: white !important; }
            .card { box-shadow: none !important; border: 1px solid #e2e8f0 !important; }
        }
    </style>
</x-app-layout>