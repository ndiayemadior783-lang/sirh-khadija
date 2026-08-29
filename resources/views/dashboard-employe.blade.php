<x-app-layout>
    <x-slot name="title">Mon espace</x-slot>
    <x-slot name="header">
        <div>
            <h1 class="text-2xl font-bold text-slate-800">Bonjour, {{ $employe?->prenom ?? auth()->user()->name }} 👋</h1>
            <p class="mt-1 text-sm text-slate-500">Votre espace personnel — {{ now()->translatedFormat('l d F Y') }}</p>
        </div>
    </x-slot>

    @if(!$employe)
        <div class="card p-6 text-sm text-slate-600">Aucune fiche employé n'est liée à votre compte. Contactez le service RH.</div>
    @else
        <!-- Cartes -->
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
            <div class="stat-card">
                <p class="text-sm font-medium text-slate-500">Mon poste</p>
                <p class="mt-1 text-lg font-bold text-slate-800">{{ $employe->poste?->libelle }}</p>
                <p class="mt-1 text-xs text-slate-400">{{ $employe->departement?->nom }}</p>
            </div>
            <div class="stat-card">
                <p class="text-sm font-medium text-slate-500">Salaire de base</p>
                <p class="mt-1 text-lg font-bold text-slate-800">{{ number_format($employe->salaire_base, 0, ',', ' ') }} F</p>
                <p class="mt-1 text-xs text-slate-400">par mois</p>
            </div>
            <div class="stat-card">
                <p class="text-sm font-medium text-slate-500">Ancienneté</p>
                <p class="mt-1 text-lg font-bold text-slate-800">{{ $employe->anciennete }} ans</p>
                <p class="mt-1 text-xs text-slate-400">depuis {{ $employe->date_embauche->format('d/m/Y') }}</p>
            </div>
            <div class="stat-card">
                <p class="text-sm font-medium text-slate-500">Mes pointages</p>
                <p class="mt-1 text-lg font-bold text-slate-800">{{ $mesPresences->count() }}</p>
                <p class="mt-1 text-xs text-slate-400">derniers enregistrements</p>
            </div>
        </div>

        <div class="mt-6 grid grid-cols-1 gap-6 lg:grid-cols-2">
            <!-- Mes congés -->
            <div class="card overflow-hidden">
                <div class="flex items-center justify-between border-b border-slate-100 px-6 py-4">
                    <h2 class="text-base font-semibold text-slate-800">Mes prochains congés</h2>
                    <a href="{{ route('conges.create') }}" class="text-sm font-semibold text-indigo-600 hover:text-indigo-800">Demander un congé</a>
                </div>
                <div class="divide-y divide-slate-100">
                    @forelse($conges as $conge)
                        <div class="flex items-center gap-3 px-6 py-3">
                            <div class="min-w-0 flex-1">
                                <p class="text-sm font-semibold text-slate-800">{{ $conge->libelleType() }}</p>
                                <p class="text-xs text-slate-500">{{ $conge->date_debut->format('d/m/Y') }} → {{ $conge->date_fin->format('d/m/Y') }} · {{ $conge->jours }} jour(s)</p>
                            </div>
                            <span class="badge @if($conge->statut === 'valide') bg-emerald-100 text-emerald-700 @elseif($conge->statut === 'refuse') bg-rose-100 text-rose-700 @else bg-amber-100 text-amber-700 @endif">{{ $conge->statut }}</span>
                        </div>
                    @empty
                        <p class="px-6 py-6 text-center text-sm text-slate-500">Aucun congé à venir.</p>
                    @endforelse
                </div>
            </div>

            <!-- Mes bulletins -->
            <div class="card overflow-hidden">
                <div class="border-b border-slate-100 px-6 py-4">
                    <h2 class="text-base font-semibold text-slate-800">Mes derniers bulletins</h2>
                </div>
                <div class="divide-y divide-slate-100">
                    @forelse($derniersBulletins as $b)
                        <a href="{{ route('bulletins.show', $b) }}" class="flex items-center justify-between px-6 py-3 hover:bg-slate-50">
                            <div>
                                <p class="text-sm font-semibold text-slate-800">{{ $b->period }}</p>
                                <p class="text-xs text-slate-500">{{ $b->numero }}</p>
                            </div>
                            <span class="text-sm font-bold text-emerald-600">{{ number_format($b->salaire_net, 0, ',', ' ') }} F</span>
                        </a>
                    @empty
                        <p class="px-6 py-6 text-center text-sm text-slate-500">Aucun bulletin disponible.</p>
                    @endforelse
                </div>
            </div>
        </div>
    @endif
</x-app-layout>