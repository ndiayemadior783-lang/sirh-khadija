<x-app-layout>
    <x-slot name="title">Livre de paie</x-slot>
    <x-slot name="header">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-2xl font-bold text-slate-800">Livre de paie</h1>
                <p class="mt-1 text-sm text-slate-500">{{ $nomMois }} {{ $annee }} &middot; {{ $nombreEmployes }} employe(s)</p>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('export.livre-paie', ['period' => $period]) }}" class="btn-primary" target="_blank"><svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg> Export PDF</a>
                <button onclick="window.print()" class="btn-secondary">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4H7v4a2 2 0 002 2z"/></svg>
                    Imprimer
                </button>
                <a href="{{ route('bulletins.index') }}" class="btn-primary">
                    Bulletins de paie
                </a>
            </div>
        </div>
    </x-slot>

    <!-- Selecteur de periode -->
    <div class="card mb-6 p-4">
        <form method="GET" action="{{ route('livre-paie.index') }}" class="flex flex-wrap items-end gap-4">
            <div>
                <label class="mb-1 block text-xs font-semibold text-slate-600">Mois / Periode</label>
                <select name="period" class="rounded-lg border border-slate-300 px-3 py-2 text-sm">
                    @foreach($periodes as $p)
                        <option value="{{ $p->period }}" @if($p->period === $period) selected @endif>{{ $p->period }}</option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="btn-secondary">Afficher</button>
        </form>
    </div>

    <!-- Totaux -->
    <div class="grid grid-cols-2 gap-4 lg:grid-cols-6">
        <div class="stat-card"><p class="text-xs font-medium text-slate-500">Salaire de base</p><p class="mt-1 text-lg font-bold text-slate-800">{{ number_format($totalBase, 0, ',', ' ') }} F</p></div>
        <div class="stat-card"><p class="text-xs font-medium text-slate-500">Brut total</p><p class="mt-1 text-lg font-bold text-slate-800">{{ number_format($totalBrut, 0, ',', ' ') }} F</p></div>
        <div class="stat-card"><p class="text-xs font-medium text-slate-500">Primes</p><p class="mt-1 text-lg font-bold text-emerald-600">{{ number_format($totalPrimes, 0, ',', ' ') }} F</p></div>
        <div class="stat-card"><p class="text-xs font-medium text-slate-500">Cotisations</p><p class="mt-1 text-lg font-bold text-rose-600">{{ number_format($totalCotisations, 0, ',', ' ') }} F</p></div>
        <div class="stat-card"><p class="text-xs font-medium text-slate-500">Impots</p><p class="mt-1 text-lg font-bold text-rose-600">{{ number_format($totalImpots, 0, ',', ' ') }} F</p></div>
        <div class="stat-card"><p class="text-xs font-medium text-slate-500">Net a payer</p><p class="mt-1 text-lg font-bold text-indigo-700">{{ number_format($totalNet, 0, ',', ' ') }} F</p></div>
    </div>

    <!-- Tableau du livre de paie -->
    <div class="card mt-6 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="px-3 py-3 text-left text-[10px] font-semibold uppercase tracking-wider text-slate-500">Mat.</th>
                        <th class="px-3 py-3 text-left text-[10px] font-semibold uppercase tracking-wider text-slate-500">Employe</th>
                        <th class="px-3 py-3 text-right text-[10px] font-semibold uppercase tracking-wider text-slate-500">Base</th>
                        <th class="px-3 py-3 text-right text-[10px] font-semibold uppercase tracking-wider text-slate-500">Primes</th>
                        <th class="px-3 py-3 text-right text-[10px] font-semibold uppercase tracking-wider text-slate-500">Heures sup</th>
                        <th class="px-3 py-3 text-right text-[10px] font-semibold uppercase tracking-wider text-slate-500">Indem.</th>
                        <th class="px-3 py-3 text-right text-[10px] font-semibold uppercase tracking-wider text-slate-500">Brut</th>
                        <th class="px-3 py-3 text-right text-[10px] font-semibold uppercase tracking-wider text-slate-500">Cotis.</th>
                        <th class="px-3 py-3 text-right text-[10px] font-semibold uppercase tracking-wider text-slate-500">Impots</th>
                        <th class="px-3 py-3 text-right text-[10px] font-semibold uppercase tracking-wider text-slate-500">Net</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 bg-white">
                    @forelse($bulletins as $b)
                    <tr class="hover:bg-slate-50">
                        <td class="px-3 py-2 text-xs text-slate-500">{{ $b->employee?->matricule ?? '—' }}</td>
                        <td class="px-3 py-2 text-xs font-semibold text-slate-800">{{ $b->employee?->nomComplet }}</td>
                        <td class="px-3 py-2 text-right text-xs text-slate-700">{{ number_format($b->salaire_base, 0, ',', ' ') }}</td>
                        <td class="px-3 py-2 text-right text-xs text-emerald-600">{{ number_format($b->primes, 0, ',', ' ') }}</td>
                        <td class="px-3 py-2 text-right text-xs text-emerald-600">{{ number_format($b->heures_sup, 0, ',', ' ') }}</td>
                        <td class="px-3 py-2 text-right text-xs text-slate-700">{{ number_format($b->indemnites, 0, ',', ' ') }}</td>
                        <td class="px-3 py-2 text-right text-xs font-semibold text-slate-800">{{ number_format($b->salaire_brut, 0, ',', ' ') }}</td>
                        <td class="px-3 py-2 text-right text-xs text-rose-600">{{ number_format($b->cotisations_sociales, 0, ',', ' ') }}</td>
                        <td class="px-3 py-2 text-right text-xs text-rose-600">{{ number_format($b->impots, 0, ',', ' ') }}</td>
                        <td class="px-3 py-2 text-right text-xs font-bold text-indigo-700">{{ number_format($b->salaire_net, 0, ',', ' ') }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="10" class="px-6 py-10 text-center text-sm text-slate-500">Aucun bulletin pour cette periode. Generer d abord les bulletins de paie.</td></tr>
                    @endforelse
                </tbody>
                <tfoot class="bg-indigo-50">
                    <tr>
                        <td class="px-3 py-3 text-xs font-bold text-slate-800" colspan="2">TOTAUX</td>
                        <td class="px-3 py-3 text-right text-xs font-bold text-slate-800">{{ number_format($totalBase, 0, ',', ' ') }}</td>
                        <td class="px-3 py-3 text-right text-xs font-bold text-emerald-600">{{ number_format($totalPrimes, 0, ',', ' ') }}</td>
                        <td class="px-3 py-3 text-right text-xs font-bold text-emerald-600">{{ number_format($totalHS, 0, ',', ' ') }}</td>
                        <td class="px-3 py-3 text-right text-xs font-bold text-slate-800">{{ number_format($totalIndemnites, 0, ',', ' ') }}</td>
                        <td class="px-3 py-3 text-right text-xs font-bold text-slate-800">{{ number_format($totalBrut, 0, ',', ' ') }}</td>
                        <td class="px-3 py-3 text-right text-xs font-bold text-rose-600">{{ number_format($totalCotisations, 0, ',', ' ') }}</td>
                        <td class="px-3 py-3 text-right text-xs font-bold text-rose-600">{{ number_format($totalImpots, 0, ',', ' ') }}</td>
                        <td class="px-3 py-3 text-right text-xs font-bold text-indigo-700">{{ number_format($totalNet, 0, ',', ' ') }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

    <style>
        @media print {
            aside, header, nav, .btn-secondary, .btn-primary { display: none !important; }
            main { padding: 0 !important; max-width: 100% !important; }
            body { background: white !important; }
            .card { box-shadow: none !important; border: 1px solid #e2e8f0 !important; }
            table { font-size: 10px !important; }
            th, td { padding: 3px 6px !important; }
        }
    </style>
</x-app-layout>
