@php
    $cotisations = $bulletin->cotisations_sociales + $bulletin->impots;
@endphp
<x-app-layout>
    <x-slot name="title">Bulletin {{ $bulletin->numero }}</x-slot>
    <x-slot name="header">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-2xl font-bold text-slate-800">Bulletin de paie</h1>
                <p class="mt-1 text-sm text-slate-500">{{ $bulletin->numero }} · Période {{ $bulletin->period }}</p>
            </div>
            <button onclick="window.print()" class="btn-secondary">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4H7v4a2 2 0 002 2z"/></svg>
                Imprimer
            </button>
        </div>
    </x-slot>

    <div class="card mx-auto max-w-3xl overflow-hidden">
        <div class="border-b border-slate-100 bg-indigo-50 px-8 py-6">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-lg font-bold text-indigo-800">KHADIJA CONSULTING</h2>
                    <p class="text-xs text-slate-500">Cabinet de conseil en Ressources Humaines</p>
                    <p class="text-xs text-slate-500">Dakar, Sénégal · contact@khadijaconsulting.sn</p>
                </div>
                <div class="text-right">
                    <p class="text-sm font-semibold text-slate-800">Bulletin de salaire</p>
                    <p class="text-xs text-slate-500">{{ $bulletin->numero }}</p>
                    <p class="text-xs text-slate-500">Période : {{ $bulletin->period }}</p>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 gap-4 border-b border-slate-100 px-8 py-6 sm:grid-cols-2">
            <div>
                <p class="text-xs font-semibold uppercase tracking-wider text-slate-400">Employé</p>
                <p class="mt-1 text-base font-bold text-slate-800">{{ $bulletin->employee?->nomComplet }}</p>
                <p class="text-sm text-slate-500">{{ $bulletin->employee?->poste?->libelle }}</p>
                <p class="text-xs text-slate-500">Matricule : {{ $bulletin->employee?->matricule }}</p>
            </div>
            <div>
                <p class="text-xs font-semibold uppercase tracking-wider text-slate-400">Département</p>
                <p class="mt-1 text-sm text-slate-700">{{ $bulletin->employee?->departement?->nom ?? '—' }}</p>
                <p class="mt-2 text-xs font-semibold uppercase tracking-wider text-slate-400">Date d'émission</p>
                <p class="text-sm text-slate-700">{{ now()->format('d/m/Y') }}</p>
            </div>
        </div>

        <div class="grid grid-cols-1 gap-0 px-8 py-6 sm:grid-cols-2">
            <div>
                <h3 class="mb-3 text-sm font-semibold text-slate-700">Éléments du salaire</h3>
                <div class="space-y-2">
                    <div class="flex justify-between text-sm"><span class="text-slate-500">Salaire de base</span><span class="font-medium text-slate-800">{{ number_format($bulletin->salaire_base, 2, ',', ' ') }} F</span></div>
                    <div class="flex justify-between text-sm"><span class="text-slate-500">Primes</span><span class="font-medium text-emerald-600">+ {{ number_format($bulletin->primes, 2, ',', ' ') }} F</span></div>
                    <div class="flex justify-between text-sm"><span class="text-slate-500">Heures supplémentaires</span><span class="font-medium text-emerald-600">+ {{ number_format($bulletin->heures_sup, 2, ',', ' ') }} F</span></div>
                    <div class="flex justify-between text-sm"><span class="text-slate-500">Indemnités</span><span class="font-medium text-emerald-600">+ {{ number_format($bulletin->indemnites, 2, ',', ' ') }} F</span></div>
                </div>
                <div class="mt-4 flex justify-between border-t border-slate-200 pt-3">
                    <span class="text-sm font-bold text-slate-800">Salaire brut</span>
                    <span class="text-sm font-bold text-slate-800">{{ number_format($bulletin->salaire_brut, 2, ',', ' ') }} F</span>
                </div>
            </div>
            <div class="mt-6 sm:mt-0">
                <h3 class="mb-3 text-sm font-semibold text-slate-700">Retenues</h3>
                <div class="space-y-2">
                    <div class="flex justify-between text-sm"><span class="text-slate-500">Cotisations sociales</span><span class="font-medium text-rose-600">- {{ number_format($bulletin->cotisations_sociales, 2, ',', ' ') }} F</span></div>
                    <div class="flex justify-between text-sm"><span class="text-slate-500">Impôt sur revenu</span><span class="font-medium text-rose-600">- {{ number_format($bulletin->impots, 2, ',', ' ') }} F</span></div>
                </div>
                <div class="mt-4 flex justify-between border-t border-slate-200 pt-3">
                    <span class="text-sm font-bold text-slate-800">Total retenues</span>
                    <span class="text-sm font-bold text-rose-600">- {{ number_format($cotisations, 2, ',', ' ') }} F</span>
                </div>
            </div>
        </div>

        <div class="mx-8 mb-6 rounded-xl bg-emerald-50 px-6 py-4">
            <div class="flex items-center justify-between">
                <span class="text-sm font-bold text-emerald-800">NET À PAYER</span>
                <span class="text-xl font-extrabold text-emerald-700">{{ number_format($bulletin->salaire_net, 2, ',', ' ') }} FCFA</span>
            </div>
        </div>

        <div class="border-t border-slate-100 bg-slate-50 px-8 py-4 text-center text-xs text-slate-400">
            Document généré par le SIRH de Khadija Consulting · {{ now()->format('d/m/Y H:i') }}
        </div>
    </div>

    <style>
        @media print {
            aside, .lg\:pl-64, .btn-secondary, header { display: none !important; }
            main { padding: 0 !important; max-width: 100% !important; }
            body { background: white !important; }
            .card { box-shadow: none !important; border: 1px solid #e2e8f0 !important; }
        }
    </style>
</x-app-layout>