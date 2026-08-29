<x-app-layout>
    <x-slot name="title">Bulletins de paie</x-slot>
    <x-slot name="header">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-2xl font-bold text-slate-800">Bulletins de paie</h1>
                <p class="mt-1 text-sm text-slate-500">Paie du personnel — période {{ $period }}</p>
            </div>
            <form method="POST" action="{{ route('bulletins.generer') }}" class="flex items-center gap-2">
                @csrf
                <input type="month" name="period" value="{{ $period }}" class="rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                <button type="submit" class="btn-primary">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                    Générer
                </button>
            </form>
        </div>
    </x-slot>

    <!-- Totaux de la période -->
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
        <div class="stat-card">
            <p class="text-sm font-medium text-slate-500">Masse salariale brute</p>
            <p class="mt-1 text-2xl font-bold text-slate-800">{{ number_format($totalBrut, 0, ',', ' ') }} F</p>
            <p class="mt-1 text-xs text-slate-400">{{ $bulletins->count() }} bulletins</p>
        </div>
        <div class="stat-card">
            <p class="text-sm font-medium text-slate-500">Cotisations & impôts</p>
            <p class="mt-1 text-2xl font-bold text-rose-600">{{ number_format($totalCotisations, 0, ',', ' ') }} F</p>
        </div>
        <div class="stat-card">
            <p class="text-sm font-medium text-slate-500">Net total à payer</p>
            <p class="mt-1 text-2xl font-bold text-emerald-600">{{ number_format($totalNet, 0, ',', ' ') }} F</p>
        </div>
    </div>

    <div class="card mt-6 overflow-hidden">
        <div class="flex flex-wrap items-center gap-3 border-b border-slate-100 px-6 py-4">
            <h2 class="text-base font-semibold text-slate-800">Bulletins du mois</h2>
            <div class="ml-auto flex flex-wrap gap-2">
                @foreach($periodes as $p)
                    <a href="{{ route('bulletins.index', ['period' => $p->period]) }}"
                       class="rounded-lg px-3 py-1.5 text-xs font-semibold {{ $p->period === $period ? 'bg-indigo-600 text-white' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                        {{ $p->period }}
                    </a>
                @endforeach
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">N° Bulletin</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">Employé</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">Salaire de base</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">Primes</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">Brut</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">Cotisations</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">Net</th>
                        <th class="px-6 py-3 text-right text-xs font-semibold uppercase tracking-wider text-slate-500">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 bg-white">
                    @forelse($bulletins as $bulletin)
                        <tr class="hover:bg-slate-50">
                            <td class="px-6 py-4 text-sm font-semibold text-slate-700">{{ $bulletin->numero }}</td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="flex h-9 w-9 items-center justify-center rounded-full bg-indigo-100 text-xs font-bold text-indigo-700">{{ $bulletin->employee?->initiales }}</div>
                                    <a href="{{ route('employes.show', $bulletin->employee) }}" class="text-sm font-semibold text-slate-800 hover:text-indigo-600">{{ $bulletin->employee?->nomComplet }}</a>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-sm text-slate-600">{{ number_format($bulletin->salaire_base, 0, ',', ' ') }}</td>
                            <td class="px-6 py-4 text-sm text-slate-600">{{ number_format($bulletin->primes, 0, ',', ' ') }}</td>
                            <td class="px-6 py-4 text-sm font-semibold text-slate-700">{{ number_format($bulletin->salaire_brut, 0, ',', ' ') }}</td>
                            <td class="px-6 py-4 text-sm text-rose-600">{{ number_format($bulletin->cotisations_sociales + $bulletin->impots, 0, ',', ' ') }}</td>
                            <td class="px-6 py-4 text-sm font-bold text-emerald-600">{{ number_format($bulletin->salaire_net, 0, ',', ' ') }}</td>
                            <td class="px-6 py-4 text-right">
                                <a href="{{ route('bulletins.show', $bulletin) }}" class="btn-secondary !px-3 !py-1.5 !text-xs">Voir</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-10 text-center text-sm text-slate-500">
                                Aucun bulletin pour cette période.
                                <button onclick="document.querySelector('input[name=period]')?.showPicker?.()" class="ml-2 font-semibold text-indigo-600">Générer les bulletins</button>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>