<x-app-layout>
    <x-slot name="title">{{ $employe->nomComplet }}</x-slot>
    <x-slot name="header">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div class="flex items-center gap-4">
                <div class="flex h-14 w-14 items-center justify-center rounded-full bg-indigo-600 text-lg font-bold text-white">{{ $employe->initiales }}</div>
                <div>
                    <h1 class="text-2xl font-bold text-slate-800">{{ $employe->nomComplet }}</h1>
                    <p class="text-sm text-slate-500">{{ $employe->poste?->libelle }} · {{ $employe->matricule }}</p>
                </div>
                <span class="badge {{ $employe->statut === 'actif' ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-100 text-slate-600' }}">{{ $employe->statut }}</span>
            </div>
            <details class="relative">
                <summary class="btn-secondary cursor-pointer list-none"><svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg> Attestations <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg></summary>
                <div class="absolute right-0 z-30 mt-2 w-64 rounded-xl border border-slate-200 bg-white p-2 shadow-xl">
                    <a href="{{ route('export.attestation-travail', $employe) }}" target="_blank" class="flex items-center gap-2 rounded-lg px-3 py-2 text-sm text-slate-700 hover:bg-indigo-50">
                        <svg class="h-4 w-4 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        <span class="font-medium">Attestation de travail</span>
                    </a>
                    <a href="{{ route('export.attestation-salaire', $employe) }}" target="_blank" class="flex items-center gap-2 rounded-lg px-3 py-2 text-sm text-slate-700 hover:bg-indigo-50">
                        <svg class="h-4 w-4 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <span class="font-medium">Attestation de salaire</span>
                    </a>
                    <a href="{{ route('export.certificat-travail', $employe) }}" target="_blank" class="flex items-center gap-2 rounded-lg px-3 py-2 text-sm text-slate-700 hover:bg-indigo-50">
                        <svg class="h-4 w-4 text-amber-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        <span class="font-medium">Certificat de travail</span>
                    </a>
                </div>
            </details>
                employes.edit', $employe) }}" class="btn-primary">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                Modifier
            </a>
            <button onclick="window.print()" class="btn-secondary">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4H7v4a2 2 0 002 2z"/></svg>
                Imprimer
            </button>
        </div>
    </x-slot>

    <!-- Statistiques de l'employé -->
    <div class="grid grid-cols-2 gap-4 lg:grid-cols-4">
        <div class="stat-card">
            <p class="text-sm font-medium text-slate-500">Ancienneté</p>
            <p class="mt-1 text-2xl font-bold text-slate-800">{{ $employe->anciennete }} ans</p>
            <p class="mt-1 text-xs text-slate-400">depuis {{ $employe->date_embauche->format('d/m/Y') }}</p>
        </div>
        <div class="stat-card">
            <p class="text-sm font-medium text-slate-500">Présences (mois)</p>
            <p class="mt-1 text-2xl font-bold text-slate-800">{{ $presenceCeMois }}</p>
            <p class="mt-1 text-xs text-slate-400">pointages ce mois</p>
        </div>
        <div class="stat-card">
            <p class="text-sm font-medium text-slate-500">Congés validés</p>
            <p class="mt-1 text-2xl font-bold text-slate-800">{{ $congeValideCeMois }}</p>
            <p class="mt-1 text-xs text-slate-400">congés accordés</p>
        </div>
        <div class="stat-card">
            <p class="text-sm font-medium text-slate-500">Salaire de base</p>
            <p class="mt-1 text-2xl font-bold text-slate-800">{{ number_format($employe->salaire_base, 0, ',', ' ') }} F</p>
            <p class="mt-1 text-xs text-slate-400">par mois</p>
        </div>
    </div>

    <!-- Solde de congés -->
    <div class="card mt-6 overflow-hidden">
        <div class="border-b border-slate-100 px-6 py-4">
            <h2 class="text-base font-semibold text-slate-800">Solde de congés payés — {{ now()->year }}</h2>
        </div>
        <div class="grid grid-cols-1 gap-4 p-6 sm:grid-cols-3">
            <div>
                <p class="text-xs font-medium text-slate-500">Droits annuels</p>
                <p class="mt-1 text-2xl font-bold text-slate-800">{{ $employe->joursCongeAnnuel() }} jours</p>
            </div>
            <div>
                <p class="text-xs font-medium text-slate-500">Jours pris (validés)</p>
                <p class="mt-1 text-2xl font-bold text-amber-600">{{ $employe->congesPris() }} jours</p>
            </div>
            <div>
                <p class="text-xs font-medium text-slate-500">Reste à prendre</p>
                <p class="mt-1 text-2xl font-bold {{ $employe->soldeConges() > 0 ? 'text-emerald-600' : 'text-rose-600' }}">{{ $employe->soldeConges() }} jours</p>
            </div>
        </div>
    </div>

    <div class="mt-6 grid grid-cols-1 gap-6 lg:grid-cols-2">
        <!-- Informations personnelles -->
        <div class="card overflow-hidden">
            <div class="border-b border-slate-100 px-6 py-4">
                <h2 class="text-base font-semibold text-slate-800">Informations personnelles</h2>
            </div>
            <dl class="divide-y divide-slate-100">
                <div class="grid grid-cols-3 px-6 py-3">
                    <dt class="text-sm font-medium text-slate-500">Nom complet</dt>
                    <dd class="col-span-2 text-sm text-slate-800">{{ $employe->nomComplet }}</dd>
                </div>
                <div class="grid grid-cols-3 px-6 py-3">
                    <dt class="text-sm font-medium text-slate-500">Genre</dt>
                    <dd class="col-span-2 text-sm text-slate-800">{{ $employe->genre === 'M' ? 'Masculin' : 'Féminin' }}</dd>
                </div>
                <div class="grid grid-cols-3 px-6 py-3">
                    <dt class="text-sm font-medium text-slate-500">Date de naissance</dt>
                    <dd class="col-span-2 text-sm text-slate-800">{{ $employe->date_naissance?->format('d/m/Y') ?? '—' }}</dd>
                </div>
                <div class="grid grid-cols-3 px-6 py-3">
                    <dt class="text-sm font-medium text-slate-500">Lieu de naissance</dt>
                    <dd class="col-span-2 text-sm text-slate-800">{{ $employe->lieu_naissance ?? '—' }}</dd>
                </div>
                <div class="grid grid-cols-3 px-6 py-3">
                    <dt class="text-sm font-medium text-slate-500">N° pièce</dt>
                    <dd class="col-span-2 text-sm text-slate-800">{{ $employe->cin ?? '—' }}</dd>
                </div>
                <div class="grid grid-cols-3 px-6 py-3">
                    <dt class="text-sm font-medium text-slate-500">Nationalité</dt>
                    <dd class="col-span-2 text-sm text-slate-800">{{ $employe->nationalite ?? '—' }}</dd>
                </div>
                <div class="grid grid-cols-3 px-6 py-3">
                    <dt class="text-sm font-medium text-slate-500">Adresse</dt>
                    <dd class="col-span-2 text-sm text-slate-800">{{ $employe->adresse }} {{ $employe->ville ? ', ' . $employe->ville : '' }}</dd>
                </div>
                <div class="grid grid-cols-3 px-6 py-3">
                    <dt class="text-sm font-medium text-slate-500">Téléphone</dt>
                    <dd class="col-span-2 text-sm text-slate-800">{{ $employe->telephone ?? '—' }}</dd>
                </div>
                <div class="grid grid-cols-3 px-6 py-3">
                    <dt class="text-sm font-medium text-slate-500">Email</dt>
                    <dd class="col-span-2 text-sm text-slate-800">{{ $employe->email ?? '—' }}</dd>
                </div>
            </dl>
        </div>

        <!-- Informations professionnelles -->
        <div class="card overflow-hidden">
            <div class="border-b border-slate-100 px-6 py-4">
                <h2 class="text-base font-semibold text-slate-800">Informations professionnelles</h2>
            </div>
            <dl class="divide-y divide-slate-100">
                <div class="grid grid-cols-3 px-6 py-3">
                    <dt class="text-sm font-medium text-slate-500">Poste</dt>
                    <dd class="col-span-2 text-sm text-slate-800">{{ $employe->poste?->libelle ?? '—' }}</dd>
                </div>
                <div class="grid grid-cols-3 px-6 py-3">
                    <dt class="text-sm font-medium text-slate-500">Département</dt>
                    <dd class="col-span-2 text-sm text-slate-800">{{ $employe->departement?->nom ?? '—' }}</dd>
                </div>
                <div class="grid grid-cols-3 px-6 py-3">
                    <dt class="text-sm font-medium text-slate-500">Responsable</dt>
                    <dd class="col-span-2 text-sm text-slate-800">{{ $employe->responsable?->nomComplet ?? '—' }}</dd>
                </div>
                <div class="grid grid-cols-3 px-6 py-3">
                    <dt class="text-sm font-medium text-slate-500">Date d'embauche</dt>
                    <dd class="col-span-2 text-sm text-slate-800">{{ $employe->date_embauche->format('d/m/Y') }}</dd>
                </div>
                <div class="grid grid-cols-3 px-6 py-3">
                    <dt class="text-sm font-medium text-slate-500">Contrat</dt>
                    <dd class="col-span-2 text-sm text-slate-800">{{ $dernierContrat?->libelleType() ?? '—' }} · {{ $dernierContrat?->statut ?? '' }}</dd>
                </div>
                <div class="grid grid-cols-3 px-6 py-3">
                    <dt class="text-sm font-medium text-slate-500">Compte de connexion</dt>
                    <dd class="col-span-2 text-sm text-slate-800">{{ $employe->user ? $employe->user->email . ' (' . $employe->user->libelleRole() . ')' : 'Non créé' }}</dd>
                </div>
            </dl>
        </div>
    </div>

    <!-- Dernier bulletin -->
    @if($dernierBulletin)
    <div class="card mt-6 overflow-hidden">
        <div class="border-b border-slate-100 px-6 py-4">
            <h2 class="text-base font-semibold text-slate-800">Dernier bulletin de paie — {{ $dernierBulletin->period }}</h2>
        </div>
        <div class="grid grid-cols-2 gap-4 p-6 md:grid-cols-5">
            <div>
                <p class="text-xs font-medium text-slate-500">Brut</p>
                <p class="mt-1 text-lg font-bold text-slate-800">{{ number_format($dernierBulletin->salaire_brut, 0, ',', ' ') }} F</p>
            </div>
            <div>
                <p class="text-xs font-medium text-slate-500">Primes</p>
                <p class="mt-1 text-sm font-semibold text-slate-700">{{ number_format($dernierBulletin->primes, 0, ',', ' ') }} F</p>
            </div>
            <div>
                <p class="text-xs font-medium text-slate-500">Cotisations</p>
                <p class="mt-1 text-sm font-semibold text-slate-700">{{ number_format($dernierBulletin->cotisations_sociales + $dernierBulletin->impots, 0, ',', ' ') }} F</p>
            </div>
            <div>
                <p class="text-xs font-medium text-slate-500">Net à payer</p>
                <p class="mt-1 text-lg font-bold text-emerald-600">{{ number_format($dernierBulletin->salaire_net, 0, ',', ' ') }} F</p>
            </div>
            <div class="flex items-end">
                <a href="{{ route('bulletins.show', $dernierBulletin) }}" class="btn-secondary">Voir le bulletin</a>
            </div>
        </div>
    </div>
    @endif

    <style>
        @media print {
            aside, .lg\:pl-64, .btn-secondary, .btn-primary, header, nav { display: none !important; }
            main { padding: 0 !important; max-width: 100% !important; }
            body { background: white !important; }
            .card { box-shadow: none !important; border: 1px solid #e2e8f0 !important; }
        }
    </style>
</x-app-layout>
