<x-app-layout>
    <x-slot name="title">Nouveau contrat</x-slot>
    <x-slot name="header">
        <h1 class="text-2xl font-bold text-slate-800">Enregistrer un contrat</h1>
    </x-slot>

    <form method="POST" action="{{ route('contrats.store') }}" class="max-w-2xl">
        @csrf
        <div class="card p-6">
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                <div class="sm:col-span-2">
                    <x-input-label for="employee_id" value="Employé" />
                    <select id="employee_id" name="employee_id" class="mt-1 block w-full rounded-lg border-slate-300 focus:border-indigo-500 focus:ring-indigo-500" required>
                        <option value="">Sélectionner...</option>
                        @foreach($employes as $emp)
                            <option value="{{ $emp->id }}" @selected(old('employee_id') == $emp->id)>{{ $emp->nomComplet }} ({{ $emp->matricule }})</option>
                        @endforeach
                    </select>
                    <x-input-error :messages="$errors->get('employee_id')" class="mt-2" />
                </div>
                <div>
                    <x-input-label for="type" value="Type de contrat" />
                    <select id="type" name="type" class="mt-1 block w-full rounded-lg border-slate-300 focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="CDI" @selected(old('type', 'CDI') == 'CDI')>CDI</option>
                        <option value="CDD" @selected(old('type') == 'CDD')>CDD</option>
                        <option value="stage" @selected(old('type') == 'stage')>Stage</option>
                        <option value="freelance" @selected(old('type') == 'freelance')>Freelance</option>
                    </select>
                </div>
                <div>
                    <x-input-label for="statut" value="Statut" />
                    <select id="statut" name="statut" class="mt-1 block w-full rounded-lg border-slate-300 focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="en_cours">En cours</option>
                        <option value="termine">Terminé</option>
                    </select>
                </div>
                <div>
                    <x-input-label for="date_debut" value="Date de début" />
                    <x-text-input id="date_debut" name="date_debut" type="date" class="mt-1 block w-full" required value="{{ old('date_debut', now()->toDateString()) }}" />
                    <x-input-error :messages="$errors->get('date_debut')" class="mt-2" />
                </div>
                <div>
                    <x-input-label for="date_fin" value="Date de fin (si CDD/Stage)" />
                    <x-text-input id="date_fin" name="date_fin" type="date" class="mt-1 block w-full" value="{{ old('date_fin') }}" />
                    <x-input-error :messages="$errors->get('date_fin')" class="mt-2" />
                </div>
                <div class="sm:col-span-2">
                    <x-input-label for="salaire_base" value="Salaire de base (FCFA)" />
                    <x-text-input id="salaire_base" name="salaire_base" type="number" step="0.01" min="0" class="mt-1 block w-full" required value="{{ old('salaire_base', 0) }}" />
                    <x-input-error :messages="$errors->get('salaire_base')" class="mt-2" />
                </div>
            </div>
        </div>
        <div class="mt-6 flex items-center gap-3">
            <button type="submit" class="btn-primary">Enregistrer</button>
            <a href="{{ route('contrats.index') }}" class="btn-secondary">Annuler</a>
        </div>
    </form>
</x-app-layout>