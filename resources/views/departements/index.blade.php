<x-app-layout>
    <x-slot name="title">Départements</x-slot>
    <x-slot name="header">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-2xl font-bold text-slate-800">Départements du cabinet</h1>
                <p class="mt-1 text-sm text-slate-500">{{ $total }} départements · {{ $totalEmployes }} employés</p>
            </div>
            <a href="{{ route('departements.create') }}" class="btn-primary">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Nouveau département
            </a>
        </div>
    </x-slot>

    <div class="grid grid-cols-1 gap-6 md:grid-cols-2 xl:grid-cols-3">
        @forelse($departements as $dep)
            <div class="card p-6">
                <div class="flex items-start justify-between">
                    <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-indigo-50 text-xl text-indigo-600">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                    </div>
                    <form method="POST" action="{{ route('departements.destroy', $dep) }}" onsubmit="return confirm('Supprimer ce département ?')">
                        @csrf
                        @method('DELETE')
                        <button class="text-slate-400 hover:text-rose-600">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                        </button>
                    </form>
                </div>
                <h2 class="mt-4 text-base font-semibold text-slate-800">{{ $dep->nom }}</h2>
                <p class="mt-1 text-sm text-slate-500">{{ $dep->description ?? 'Aucune description' }}</p>
                @if($dep->responsable)
                    <p class="mt-2 text-xs font-medium text-slate-600">Responsable : <span class="text-indigo-600">{{ $dep->responsable }}</span></p>
                @endif
                <div class="mt-4 grid grid-cols-2 gap-3 border-t border-slate-100 pt-4">
                    <div>
                        <p class="text-xs text-slate-500">Employés</p>
                        <p class="text-xl font-bold text-slate-800">{{ $dep->employes_count }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-slate-500">Postes</p>
                        <p class="text-xl font-bold text-slate-800">{{ $dep->postes_count }}</p>
                    </div>
                </div>
            </div>
        @empty
            <p class="col-span-full py-10 text-center text-sm text-slate-500">Aucun département.</p>
        @endforelse
    </div>
</x-app-layout>