<x-app-layout>
    <x-slot name="title">Pointer un employé</x-slot>
    <x-slot name="header">
        <h1 class="text-2xl font-bold text-slate-800">Pointer un employé</h1>
    </x-slot>

    <form method="POST" action="{{ route('presences.store') }}" class="max-w-2xl">
        @csrf
        <div class="card p-6">
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                <div class="sm:col-span-2">
                    <x-input-label for="employee_id" value="Employé" />
                    <select id="employee_id" name="employee_id" class="mt-1 block w-full rounded-lg border-slate-300 focus:border-indigo-500 focus:ring-indigo-500" required>
                        <option value="">Sélectionner...</option>
                        @foreach($employes as $emp)
                            <option value="{{ $emp->id }}" @selected(old('employee_id') == $emp->id)>{{ $emp->nomComplet }} ({{ $emp->poste?->libelle }})</option>
                        @endforeach
                    </select>
                    <x-input-error :messages="$errors->get('employee_id')" class="mt-2" />
                </div>
                <div>
                    <x-input-label for="date" value="Date" />
                    <x-text-input id="date" name="date" type="date" class="mt-1 block w-full" required value="{{ old('date', now()->toDateString()) }}" />
                </div>
                <div>
                    <x-input-label for="statut" value="Statut" />
                    <select id="statut" name="statut" class="mt-1 block w-full rounded-lg border-slate-300 focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="present">Présent</option>
                        <option value="absent">Absent</option>
                        <option value="conge">Congé</option>
                        <option value="maladie">Maladie</option>
                        <option value="teletravail">Télétravail</option>
                    </select>
                </div>
                <div>
                    <x-input-label for="heure_arrivee" value="Heure d'arrivée" />
                    <x-text-input id="heure_arrivee" name="heure_arrivee" type="time" class="mt-1 block w-full" value="{{ old('heure_arrivee', '08:00') }}" />
                </div>
                <div>
                    <x-input-label for="heure_depart" value="Heure de départ" />
                    <x-text-input id="heure_depart" name="heure_depart" type="time" class="mt-1 block w-full" value="{{ old('heure_depart', '17:30') }}" />
                </div>
                <div>
                    <x-input-label for="heures_sup" value="Heures supplémentaires" />
                    <x-text-input id="heures_sup" name="heures_sup" type="number" step="0.5" min="0" max="24" class="mt-1 block w-full" value="{{ old('heures_sup', 0) }}" />
                </div>
                <div class="sm:col-span-2">
                    <x-input-label for="notes" value="Notes" />
                    <textarea id="notes" name="notes" rows="2" class="mt-1 block w-full rounded-lg border-slate-300 focus:border-indigo-500 focus:ring-indigo-500">{{ old('notes') }}</textarea>
                </div>
            </div>
        </div>
        <div class="mt-6 flex items-center gap-3">
            <button type="submit" class="btn-primary">Enregistrer le pointage</button>
            <a href="{{ route('presences.index') }}" class="btn-secondary">Annuler</a>
        </div>
    </form>
</x-app-layout>