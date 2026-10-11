<x-app-layout>
    <x-slot name="title">Primes & Indemnités</x-slot>
    <x-slot name="header">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-2xl font-bold text-slate-800">Primes & Indemnités</h1>
                <p class="mt-1 text-sm text-slate-500">Gestion des primes et éléments variables de rémunération</p>
            </div>
            <a href="{{ route('primes.create') }}" class="btn-primary">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Ajouter une prime
            </a>
                        <button onclick="window.print()" class="btn-secondary"><svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4H7v4a2 2 0 002 2z"/></svg> Imprimer</button>
</div>
    </x-slot>

    <div class="grid grid-cols-2 gap-4 lg:grid-cols-3">
        <div class="stat-card"><p class="text-sm font-medium text-slate-500">Total primes versées</p><p class="mt-1 text-2xl font-bold text-slate-800">{{ number_format($totaux->total ?? 0, 0, ',', ' ') }} F</p></div>
        <div class="stat-card"><p class="text-sm font-medium text-slate-500">Nombre d'opérations</p><p class="mt-1 text-2xl font-bold text-slate-800">{{ $totaux->nombre ?? 0 }}</p></div>
    </div>

    <form method="GET" action="{{ route('primes.index') }}" class="card mt-6 mb-6 p-4">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center">
            <label class="text-sm font-semibold text-slate-600">Employé :</label>
            <select name="employee_id" class="rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                <option value="">Tous</option>
                @foreach($employes as $emp)
                    <option value="{{ $emp->id }}" @selected(request('employee_id') == $emp->id)>{{ $emp->nomComplet }}</option>
                @endforeach
            </select>
            <button type="submit" class="btn-secondary">Filtrer</button>
        </div>
    </form>

    <div class="card overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">Employé</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">Libellé</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">Montant</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">Date</th>
                        <th class="px-6 py-3 text-right text-xs font-semibold uppercase tracking-wider text-slate-500">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 bg-white">
                    @forelse($primes as $prime)
                        <tr class="hover:bg-slate-50">
                            <td class="px-6 py-4">
                                <a href="{{ route('employes.show', $prime->employee) }}" class="text-sm font-semibold text-slate-800 hover:text-indigo-600">{{ $prime->employee?->nomComplet }}</a>
                            </td>
                            <td class="px-6 py-4 text-sm text-slate-600">{{ $prime->libelle }}</td>
                            <td class="px-6 py-4 text-sm font-bold text-emerald-600">+ {{ number_format($prime->montant, 0, ',', ' ') }} F</td>
                            <td class="px-6 py-4 text-sm text-slate-600">{{ $prime->date?->format('d/m/Y') }}</td>
                            <td class="px-6 py-4 text-right">
                                <form method="POST" action="{{ route('primes.destroy', $prime) }}" onsubmit="return confirm('Supprimer cette prime ?')" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button class="text-slate-400 hover:text-rose-600">
                                        <svg class="h-5 w-5 inline" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="px-6 py-10 text-center text-sm text-slate-500">Aucune prime.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="border-t border-slate-100 px-6 py-4">{{ $primes->links() }}</div>
    </div>
</x-app-layout>