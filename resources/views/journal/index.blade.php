<x-app-layout>
    <x-slot name="title">Journal d activites</x-slot>
    <x-slot name="header">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-2xl font-bold text-slate-800">Journal d activites</h1>
                <p class="mt-1 text-sm text-slate-500">Historique des actions sur le systeme</p>
            </div>
            <form method="POST" action="{{ route('journal.clear') }}" onsubmit="return confirm('Vider tout le journal ?')">
                @csrf
                <button type="submit" class="btn-secondary text-rose-600">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                    Vider le journal
                </button>
            </form>
        </div>
    </x-slot>

    <!-- Recherche -->
    <form method="GET" action="{{ route('journal.index') }}" class="card mb-6 p-4">
        <div class="flex gap-4">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Rechercher une action..." class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
            <button type="submit" class="btn-secondary">Rechercher</button>
        </div>
    </form>

    <div class="card overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">Date</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">Utilisateur</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">Action</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">Evenement</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">Details</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 bg-white">
                    @forelse($activities as $a)
                    <tr class="hover:bg-slate-50">
                        <td class="px-4 py-3 text-xs text-slate-500">{{ $a->created_at->format('d/m/Y H:i') }}</td>
                        <td class="px-4 py-3 text-xs font-semibold text-slate-800">{{ $a->causer?->name ?? 'Systeme' }}</td>
                        <td class="px-4 py-3 text-xs text-slate-600">{{ $a->description }}</td>
                        <td class="px-4 py-3 text-xs">
                            @if($a->event === 'created')
                                <span class="rounded-full bg-emerald-100 px-2 py-0.5 text-xs font-semibold text-emerald-700">Creation</span>
                            @elseif($a->event === 'updated')
                                <span class="rounded-full bg-amber-100 px-2 py-0.5 text-xs font-semibold text-amber-700">Modification</span>
                            @elseif($a->event === 'deleted')
                                <span class="rounded-full bg-rose-100 px-2 py-0.5 text-xs font-semibold text-rose-700">Suppression</span>
                            @else
                                <span class="rounded-full bg-slate-100 px-2 py-0.5 text-xs font-semibold text-slate-700">{{ $a->event }}</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-xs text-slate-500 max-w-[200px] truncate">{{ $a->properties->toJson() }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="5" class="px-6 py-10 text-center text-sm text-slate-500">Aucune activite enregistree.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="border-t border-slate-100 px-6 py-4">{{ $activities->links() }}</div>
    </div>
</x-app-layout>
