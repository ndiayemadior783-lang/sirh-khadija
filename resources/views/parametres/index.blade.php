<x-app-layout>
    <x-slot name="title">Parametres</x-slot>
    <x-slot name="header">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-2xl font-bold text-slate-800">Parametres du systeme</h1>
                <p class="mt-1 text-sm text-slate-500">Configuration de l entreprise et des taux</p>
            </div>
        </div>
    </x-slot>

    <form method="POST" action="{{ route('parametres.update') }}" class="space-y-6">
        @csrf

        @if ($errors->any())
            <div class="rounded-lg border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-800">
                <ul class="list-disc pl-4">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Entreprise -->
        <div class="card p-6">
            <h2 class="mb-4 text-base font-bold text-slate-800">Informations de l entreprise</h2>
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                <div>
                    <label class="mb-1 block text-xs font-semibold text-slate-600">Nom de l entreprise *</label>
                    <input type="text" name="nom_entreprise" value="{{ $param->nom_entreprise }}" required class="focus:ring-indigo-500 focus:border-indigo-500 block w-full rounded-lg border-slate-300 shadow-sm sm:text-sm">
                </div>
                <div>
                    <label class="mb-1 block text-xs font-semibold text-slate-600">Adresse</label>
                    <input type="text" name="adresse" value="{{ $param->adresse }}" class="focus:ring-indigo-500 focus:border-indigo-500 block w-full rounded-lg border-slate-300 shadow-sm sm:text-sm">
                </div>
                <div>
                    <label class="mb-1 block text-xs font-semibold text-slate-600">Telephone</label>
                    <input type="text" name="telephone" value="{{ $param->telephone }}" class="focus:ring-indigo-500 focus:border-indigo-500 block w-full rounded-lg border-slate-300 shadow-sm sm:text-sm">
                </div>
                <div>
                    <label class="mb-1 block text-xs font-semibold text-slate-600">Email</label>
                    <input type="email" name="email" value="{{ $param->email }}" class="focus:ring-indigo-500 focus:border-indigo-500 block w-full rounded-lg border-slate-300 shadow-sm sm:text-sm">
                </div>
                <div>
                    <label class="mb-1 block text-xs font-semibold text-slate-600">NINEA</label>
                    <input type="text" name="nine_a" value="{{ $param->nine_a }}" class="focus:ring-indigo-500 focus:border-indigo-500 block w-full rounded-lg border-slate-300 shadow-sm sm:text-sm">
                </div>
                <div>
                    <label class="mb-1 block text-xs font-semibold text-slate-600">RCCM</label>
                    <input type="text" name="rc_cm" value="{{ $param->rc_cm }}" class="focus:ring-indigo-500 focus:border-indigo-500 block w-full rounded-lg border-slate-300 shadow-sm sm:text-sm">
                </div>
            </div>
        </div>

        <!-- Taux de cotisations -->
        <div class="card p-6">
            <h2 class="mb-1 text-base font-bold text-slate-800">Taux de cotisations sociales</h2>
            <p class="mb-4 text-xs text-slate-500">En pourcentage (ex : 0.056 = 5.6%)</p>
            <div class="grid grid-cols-2 gap-4 sm:grid-cols-4">
                <div>
                    <label class="mb-1 block text-xs font-semibold text-slate-600">IPRES employeur</label>
                    <input type="number" step="0.0001" name="ipres_employeur" value="{{ $param->ipres_employeur }}" class="focus:ring-indigo-500 focus:border-indigo-500 block w-full rounded-lg border-slate-300 shadow-sm sm:text-sm">
                </div>
                <div>
                    <label class="mb-1 block text-xs font-semibold text-slate-600">IPRES employe</label>
                    <input type="number" step="0.0001" name="ipres_employe" value="{{ $param->ipres_employe }}" class="focus:ring-indigo-500 focus:border-indigo-500 block w-full rounded-lg border-slate-300 shadow-sm sm:text-sm">
                </div>
                <div>
                    <label class="mb-1 block text-xs font-semibold text-slate-600">IPM employeur</label>
                    <input type="number" step="0.0001" name="ipm_employeur" value="{{ $param->ipm_employeur }}" class="focus:ring-indigo-500 focus:border-indigo-500 block w-full rounded-lg border-slate-300 shadow-sm sm:text-sm">
                </div>
                <div>
                    <label class="mb-1 block text-xs font-semibold text-slate-600">IPM employe</label>
                    <input type="number" step="0.0001" name="ipm_employe" value="{{ $param->ipm_employe }}" class="focus:ring-indigo-500 focus:border-indigo-500 block w-full rounded-lg border-slate-300 shadow-sm sm:text-sm">
                </div>
                <div>
                    <label class="mb-1 block text-xs font-semibold text-slate-600">CFRP employeur</label>
                    <input type="number" step="0.0001" name="cfrp_employeur" value="{{ $param->cfrp_employeur }}" class="focus:ring-indigo-500 focus:border-indigo-500 block w-full rounded-lg border-slate-300 shadow-sm sm:text-sm">
                </div>
                <div>
                    <label class="mb-1 block text-xs font-semibold text-slate-600">CFRP employe</label>
                    <input type="number" step="0.0001" name="cfrp_employe" value="{{ $param->cfrp_employe }}" class="focus:ring-indigo-500 focus:border-indigo-500 block w-full rounded-lg border-slate-300 shadow-sm sm:text-sm">
                </div>
                <div>
                    <label class="mb-1 block text-xs font-semibold text-slate-600">Taxe exceptionnelle</label>
                    <input type="number" step="0.0001" name="taxe_exceptionnelle" value="{{ $param->taxe_exceptionnelle }}" class="focus:ring-indigo-500 focus:border-indigo-500 block w-full rounded-lg border-slate-300 shadow-sm sm:text-sm">
                </div>
            </div>
        </div>

        <!-- Paie -->
        <div class="card p-6">
            <h2 class="mb-4 text-base font-bold text-slate-800">Parametres de paie</h2>
            <div class="grid grid-cols-2 gap-4 sm:grid-cols-4">
                <div>
                    <label class="mb-1 block text-xs font-semibold text-slate-600">Taux cotisation globale</label>
                    <input type="number" step="0.0001" name="taux_cotisation" value="{{ $param->taux_cotisation }}" class="focus:ring-indigo-500 focus:border-indigo-500 block w-full rounded-lg border-slate-300 shadow-sm sm:text-sm">
                </div>
                <div>
                    <label class="mb-1 block text-xs font-semibold text-slate-600">Taux impot</label>
                    <input type="number" step="0.0001" name="taux_impot" value="{{ $param->taux_impot }}" class="focus:ring-indigo-500 focus:border-indigo-500 block w-full rounded-lg border-slate-300 shadow-sm sm:text-sm">
                </div>
                <div>
                    <label class="mb-1 block text-xs font-semibold text-slate-600">Taux horaire (FCFA)</label>
                    <input type="number" step="0.01" name="taux_horaire" value="{{ $param->taux_horaire }}" class="focus:ring-indigo-500 focus:border-indigo-500 block w-full rounded-lg border-slate-300 shadow-sm sm:text-sm">
                </div>
                <div>
                    <label class="mb-1 block text-xs font-semibold text-slate-600">Jours de conge paye / an</label>
                    <input type="number" name="jours_conge_paye" value="{{ $param->jours_conge_paye }}" class="focus:ring-indigo-500 focus:border-indigo-500 block w-full rounded-lg border-slate-300 shadow-sm sm:text-sm">
                </div>
            </div>
        </div>

        <div class="flex justify-end">
            <button type="submit" class="btn-primary">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                Enregistrer les parametres
            </button>
        </div>
    </form>
</x-app-layout>