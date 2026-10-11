<x-app-layout>
    <x-slot name="title">Etat des cotisations</x-slot>
    <x-slot name="header">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-2xl font-bold text-slate-800">Etat des cotisations sociales</h1>
                <p class="mt-1 text-sm text-slate-500">{{ $nomMois }} {{ $annee }} &middot; {{ $lignes->count() }} employe(s)</p>
            </div>
            <a href="{{ route('export.cotisations', ['period' => $period]) }}" class="btn-primary" target="_blank"><svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg> Export PDF</a>
                <button onclick="window.print()" class="btn-secondary">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4H7v4a2 2 0 002 2z"/></svg>
                Imprimer
            </button>
        </div>
    </x-slot>

    <!-- Selecteur de periode -->
    <div class="card mb-6 p-4">
        <form method="GET" action="{{ route('cotisations.index') }}" class="flex flex-wrap items-end gap-4">
            <div>
                <label class="mb-1 block text-xs font-semibold text-slate-600">Periode</label>
                <select name="period" class="rounded-lg border border-slate-300 px-3 py-2 text-sm">
                    @foreach($periodes as $p)
                        <option value="{{ $p->period }}" @if($p->period === $period) selected @endif>{{ $p->period }}</option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="btn-secondary">Afficher</button>
        </form>
    </div>

    <!-- Taux utilises -->
    <div class="card mb-6 p-4">
        <h3 class="mb-3 text-sm font-bold text-slate-700">Taux de cotisations appliques</h3>
        <div class="grid grid-cols-2 gap-4 lg:grid-cols-4">
            <div class="rounded-lg bg-slate-50 p-3">
                <p class="text-xs font-semibold text-slate-500">IPRES</p>
                <p class="text-sm text-slate-800">Employeur : <strong>{{ $taux["ipres_employeur"] * 100 }}%</strong></p>
                <p class="text-sm text-slate-800">Employe : <strong>{{ $taux["ipres_employe"] * 100 }}%</strong></p>
            </div>
            <div class="rounded-lg bg-slate-50 p-3">
                <p class="text-xs font-semibold text-slate-500">IPM</p>
                <p class="text-sm text-slate-800">Employeur : <strong>{{ $taux["ipm_employeur"] * 100 }}%</strong></p>
                <p class="text-sm text-slate-800">Employe : <strong>{{ $taux["ipm_employe"] * 100 }}%</strong></p>
            </div>
            <div class="rounded-lg bg-slate-50 p-3">
                <p class="text-xs font-semibold text-slate-500">CFRP</p>
                <p class="text-sm text-slate-800">Employeur : <strong>{{ $taux["cfrp_employeur"] * 100 }}%</strong></p>
                <p class="text-sm text-slate-800">Employe : <strong>{{ $taux["cfrp_employe"] * 100 }}%</strong></p>
            </div>
            <div class="rounded-lg bg-slate-50 p-3">
                <p class="text-xs font-semibold text-slate-500">Taxe exceptionnelle</p>
                <p class="text-sm text-slate-800">Taux : <strong>{{ $taux["taxe_exceptionnelle"] * 100 }}%</strong></p>
            </div>
        </div>
    </div>

    <!-- Recapitulatif -->
    <div class="grid grid-cols-2 gap-4 lg:grid-cols-4">
        <div class="stat-card"><p class="text-xs font-medium text-slate-500">Brut total</p><p class="mt-1 text-lg font-bold text-slate-800">{{ number_format($totaux["brut"], 0, ',', ' ') }} F</p></div>
        <div class="stat-card"><p class="text-xs font-medium text-slate-500">Cotisations employeur</p><p class="mt-1 text-lg font-bold text-rose-600">{{ number_format($totaux["total_employeur"], 0, ',', ' ') }} F</p></div>
        <div class="stat-card"><p class="text-xs font-medium text-slate-500">Cotisations employe</p><p class="mt-1 text-lg font-bold text-rose-600">{{ number_format($totaux["total_employe"], 0, ',', ' ') }} F</p></div>
        <div class="stat-card"><p class="text-xs font-medium text-slate-500">Impot sur le revenu</p><p class="mt-1 text-lg font-bold text-amber-600">{{ number_format($totaux["impot_revenu"], 0, ',', ' ') }} F</p></div>
    </div>

    <!-- Tableau detaille -->
    <div class="card mt-6 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="px-2 py-3 text-left text-[10px] font-semibold uppercase tracking-wider text-slate-500">Employe</th>
                        <th class="px-2 py-3 text-right text-[10px] font-semibold uppercase tracking-wider text-slate-500">Brut</th>
                        <th class="px-2 py-3 text-right text-[10px] font-semibold uppercase tracking-wider text-slate-500" colspan="2">IPRES</th>
                        <th class="px-2 py-3 text-right text-[10px] font-semibold uppercase tracking-wider text-slate-500" colspan="2">IPM</th>
                        <th class="px-2 py-3 text-right text-[10px] font-semibold uppercase tracking-wider text-slate-500" colspan="2">CFRP</th>
                        <th class="px-2 py-3 text-right text-[10px] font-semibold uppercase tracking-wider text-slate-500">Taxe 3%</th>
                        <th class="px-2 py-3 text-right text-[10px] font-semibold uppercase tracking-wider text-slate-500">Total Empl.</th>
                        <th class="px-2 py-3 text-right text-[10px] font-semibold uppercase tracking-wider text-slate-500">Total Salar.</th>
                        <th class="px-2 py-3 text-right text-[10px] font-semibold uppercase tracking-wider text-slate-500">I.R.</th>
                        <th class="px-2 py-3 text-right text-[10px] font-semibold uppercase tracking-wider text-slate-500">Net</th>
                    </tr>
                    <tr class="bg-indigo-50">
                        <th></th>
                        <th></th>
                        <th class="px-2 py-1 text-right text-[9px] font-medium text-slate-400">Emploi.</th>
                        <th class="px-2 py-1 text-right text-[9px] font-medium text-slate-400">Salari.</th>
                        <th class="px-2 py-1 text-right text-[9px] font-medium text-slate-400">Emploi.</th>
                        <th class="px-2 py-1 text-right text-[9px] font-medium text-slate-400">Salari.</th>
                        <th class="px-2 py-1 text-right text-[9px] font-medium text-slate-400">Emploi.</th>
                        <th class="px-2 py-1 text-right text-[9px] font-medium text-slate-400">Salari.</th>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 bg-white">
                    @forelse($lignes as $l)
                    <tr class="hover:bg-slate-50">
                        <td class="px-2 py-2 text-[11px] font-semibold text-slate-800">{{ $l->employee?->nomComplet }}</td>
                        <td class="px-2 py-2 text-right text-[11px] text-slate-700">{{ number_format($l->salaire_brut, 0, ',', ' ') }}</td>
                        <td class="px-2 py-2 text-right text-[11px] text-slate-600">{{ number_format($l->ipres_employeur, 0, ',', ' ') }}</td>
                        <td class="px-2 py-2 text-right text-[11px] text-rose-600">{{ number_format($l->ipres_employe, 0, ',', ' ') }}</td>
                        <td class="px-2 py-2 text-right text-[11px] text-slate-600">{{ number_format($l->ipm_employeur, 0, ',', ' ') }}</td>
                        <td class="px-2 py-2 text-right text-[11px] text-rose-600">{{ number_format($l->ipm_employe, 0, ',', ' ') }}</td>
                        <td class="px-2 py-2 text-right text-[11px] text-slate-600">{{ number_format($l->cfrp_employeur, 0, ',', ' ') }}</td>
                        <td class="px-2 py-2 text-right text-[11px] text-rose-600">{{ number_format($l->cfrp_employe, 0, ',', ' ') }}</td>
                        <td class="px-2 py-2 text-right text-[11px] text-amber-600">{{ number_format($l->taxe_exceptionnelle, 0, ',', ' ') }}</td>
                        <td class="px-2 py-2 text-right text-[11px] font-semibold text-rose-700">{{ number_format($l->total_employeur, 0, ',', ' ') }}</td>
                        <td class="px-2 py-2 text-right text-[11px] font-semibold text-rose-700">{{ number_format($l->total_employe, 0, ',', ' ') }}</td>
                        <td class="px-2 py-2 text-right text-[11px] text-amber-600">{{ number_format($l->impot_revenu, 0, ',', ' ') }}</td>
                        <td class="px-2 py-2 text-right text-[11px] font-bold text-indigo-700">{{ number_format($l->salaire_net, 0, ',', ' ') }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="13" class="px-6 py-10 text-center text-sm text-slate-500">Aucune donnee.</td></tr>
                    @endforelse
                </tbody>
                <tfoot class="bg-indigo-50">
                    <tr>
                        <td class="px-2 py-3 text-[11px] font-bold text-slate-800">TOTAUX</td>
                        <td class="px-2 py-3 text-right text-[11px] font-bold text-slate-800">{{ number_format($totaux["brut"], 0, ',', ' ') }}</td>
                        <td class="px-2 py-3 text-right text-[11px] font-bold text-slate-700">{{ number_format($totaux["ipres_employeur"], 0, ',', ' ') }}</td>
                        <td class="px-2 py-3 text-right text-[11px] font-bold text-rose-600">{{ number_format($totaux["ipres_employe"], 0, ',', ' ') }}</td>
                        <td class="px-2 py-3 text-right text-[11px] font-bold text-slate-700">{{ number_format($totaux["ipm_employeur"], 0, ',', ' ') }}</td>
                        <td class="px-2 py-3 text-right text-[11px] font-bold text-rose-600">{{ number_format($totaux["ipm_employe"], 0, ',', ' ') }}</td>
                        <td class="px-2 py-3 text-right text-[11px] font-bold text-slate-700">{{ number_format($totaux["cfrp_employeur"], 0, ',', ' ') }}</td>
                        <td class="px-2 py-3 text-right text-[11px] font-bold text-rose-600">{{ number_format($totaux["cfrp_employe"], 0, ',', ' ') }}</td>
                        <td class="px-2 py-3 text-right text-[11px] font-bold text-amber-600">{{ number_format($totaux["taxe_exceptionnelle"], 0, ',', ' ') }}</td>
                        <td class="px-2 py-3 text-right text-[11px] font-bold text-rose-700">{{ number_format($totaux["total_employeur"], 0, ',', ' ') }}</td>
                        <td class="px-2 py-3 text-right text-[11px] font-bold text-rose-700">{{ number_format($totaux["total_employe"], 0, ',', ' ') }}</td>
                        <td class="px-2 py-3 text-right text-[11px] font-bold text-amber-600">{{ number_format($totaux["impot_revenu"], 0, ',', ' ') }}</td>
                        <td class="px-2 py-3 text-right text-[11px] font-bold text-indigo-700">{{ number_format($totaux["total_net"], 0, ',', ' ') }}</td>
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
            table { font-size: 9px !important; }
            th, td { padding: 2px 4px !important; }
        }
    </style>
</x-app-layout>
