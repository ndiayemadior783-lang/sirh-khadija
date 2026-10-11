<x-app-layout>
    <x-slot name="title">{{ $formation->titre }}</x-slot>
    <x-slot name="header">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <h1 class="text-2xl font-bold text-slate-800">{{ $formation->titre }}</h1>
            <button onclick="window.print()" class="btn-secondary">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4H7v4a2 2 0 002 2z"/></svg>
                Imprimer
            </button>
                        <button onclick="window.print()" class="btn-secondary"><svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4H7v4a2 2 0 002 2z"/></svg> Imprimer</button>
</div>
    </x-slot>

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
        <div class="card overflow-hidden lg:col-span-2">
            <div class="border-b border-slate-100 px-6 py-4">
                <h2 class="text-base font-semibold text-slate-800">Détails de la formation</h2>
            </div>
            <dl class="divide-y divide-slate-100">
                <div class="grid grid-cols-3 px-6 py-3">
                    <dt class="text-sm font-medium text-slate-500">Description</dt>
                    <dd class="col-span-2 text-sm text-slate-800">{{ $formation->description ?? '—' }}</dd>
                </div>
                <div class="grid grid-cols-3 px-6 py-3">
                    <dt class="text-sm font-medium text-slate-500">Période</dt>
                    <dd class="col-span-2 text-sm text-slate-800">{{ $formation->date_debut?->format('d/m/Y') ?? '—' }} → {{ $formation->date_fin?->format('d/m/Y') ?? '—' }}</dd>
                </div>
                <div class="grid grid-cols-3 px-6 py-3">
                    <dt class="text-sm font-medium text-slate-500">Formateur</dt>
                    <dd class="col-span-2 text-sm text-slate-800">{{ $formation->formateur ?? '—' }}</dd>
                </div>
                <div class="grid grid-cols-3 px-6 py-3">
                    <dt class="text-sm font-medium text-slate-500">Lieu</dt>
                    <dd class="col-span-2 text-sm text-slate-800">{{ $formation->lieu ?? '—' }}</dd>
                </div>
                <div class="grid grid-cols-3 px-6 py-3">
                    <dt class="text-sm font-medium text-slate-500">Coût</dt>
                    <dd class="col-span-2 text-sm font-semibold text-slate-800">{{ number_format($formation->cout, 0, ',', ' ') }} FCFA</dd>
                </div>
            </dl>
        </div>

        <div class="card overflow-hidden">
            <div class="border-b border-slate-100 px-6 py-4">
                <h2 class="text-base font-semibold text-slate-800">Inscrire un employé</h2>
            </div>
            <form method="POST" action="{{ route('formations.inscrire', $formation) }}" class="p-6">
                @csrf
                <select name="employee_id" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                    <option value="">Sélectionner un employé...</option>
                    @foreach($employesDisponibles as $emp)
                        <option value="{{ $emp->id }}">{{ $emp->nomComplet }} ({{ $emp->poste?->libelle }})</option>
                    @endforeach
                </select>
                <button type="submit" class="btn-primary mt-3 w-full">Inscrire</button>
            </form>
        </div>
    </div>

    <div class="card mt-6 overflow-hidden">
        <div class="border-b border-slate-100 px-6 py-4">
            <h2 class="text-base font-semibold text-slate-800">Participants ({{ $formation->employes->count() }})</h2>
        </div>
        <div class="divide-y divide-slate-100">
            @forelse($formation->employes as $emp)
                <div class="flex items-center justify-between px-6 py-3">
                    <div class="flex items-center gap-3">
                        <div class="flex h-9 w-9 items-center justify-center rounded-full bg-indigo-100 text-xs font-bold text-indigo-700">{{ $emp->initiales }}</div>
                        <div>
                            <a href="{{ route('employes.show', $emp) }}" class="text-sm font-semibold text-slate-800 hover:text-indigo-600">{{ $emp->nomComplet }}</a>
                            <p class="text-xs text-slate-500">{{ $emp->poste?->libelle }}</p>
                        </div>
                    </div>
                    <span class="badge bg-emerald-100 text-emerald-700">{{ $emp->pivot->statut }}</span>
                </div>
            @empty
                <p class="px-6 py-6 text-center text-sm text-slate-500">Aucun participant inscrit.</p>
            @endforelse
        </div>
    </div>

    <style>
        @media print {
            aside, header, nav { display: none !important; }
            main { padding: 0 !important; max-width: 100% !important; }
            body { background: white !important; }
            .card { box-shadow: none !important; border: 1px solid #e2e8f0 !important; }
        }
    </style>
</x-app-layout>
