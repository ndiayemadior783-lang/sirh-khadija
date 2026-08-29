<x-app-layout>
    <x-slot name="title">Contrat — {{ $contrat->employee?->nomComplet }}</x-slot>
    <x-slot name="header">
        <h1 class="text-2xl font-bold text-slate-800">Détail du contrat</h1>
    </x-slot>

    <div class="card max-w-2xl overflow-hidden">
        <div class="border-b border-slate-100 bg-indigo-50 px-6 py-4 flex items-center justify-between">
            <h2 class="text-base font-semibold text-indigo-800">{{ $contrat->libelleType() }}</h2>
            <span class="badge {{ $contrat->statut === 'en_cours' ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-100 text-slate-600' }}">{{ $contrat->statut }}</span>
        </div>
        <dl class="divide-y divide-slate-100">
            <div class="grid grid-cols-3 px-6 py-3">
                <dt class="text-sm font-medium text-slate-500">Employé</dt>
                <dd class="col-span-2 text-sm text-slate-800">
                    <a href="{{ route('employes.show', $contrat->employee) }}" class="text-indigo-600 hover:underline">{{ $contrat->employee?->nomComplet }}</a>
                </dd>
            </div>
            <div class="grid grid-cols-3 px-6 py-3">
                <dt class="text-sm font-medium text-slate-500">Matricule</dt>
                <dd class="col-span-2 text-sm text-slate-800">{{ $contrat->employee?->matricule }}</dd>
            </div>
            <div class="grid grid-cols-3 px-6 py-3">
                <dt class="text-sm font-medium text-slate-500">Date de début</dt>
                <dd class="col-span-2 text-sm text-slate-800">{{ $contrat->date_debut?->format('d/m/Y') }}</dd>
            </div>
            <div class="grid grid-cols-3 px-6 py-3">
                <dt class="text-sm font-medium text-slate-500">Date de fin</dt>
                <dd class="col-span-2 text-sm text-slate-800">{{ $contrat->date_fin?->format('d/m/Y') ?? 'Indéterminée' }}</dd>
            </div>
            <div class="grid grid-cols-3 px-6 py-3">
                <dt class="text-sm font-medium text-slate-500">Salaire de base</dt>
                <dd class="col-span-2 text-sm font-semibold text-slate-800">{{ number_format($contrat->salaire_base, 0, ',', ' ') }} FCFA</dd>
            </div>
        </dl>
        <div class="border-t border-slate-100 px-6 py-4">
            <form method="POST" action="{{ route('contrats.destroy', $contrat) }}" onsubmit="return confirm('Supprimer ce contrat ?')">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn-danger">Supprimer</button>
            </form>
        </div>
    </div>
</x-app-layout>