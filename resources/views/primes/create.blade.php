<x-app-layout>
    <x-slot name="title">Ajouter une prime</x-slot>
    <x-slot name="header">
        <h1 class="text-2xl font-bold text-slate-800">Ajouter une prime</h1>
    </x-slot>

    <form method="POST" action="{{ route('primes.store') }}" class="max-w-xl">
        @csrf
        <div class="card p-6">
            <div class="grid grid-cols-1 gap-4">
                <div>
                    <x-input-label for="employee_id" value="Employé" />
                    <select id="employee_id" name="employee_id" class="mt-1 block w-full rounded-lg border-slate-300 focus:border-indigo-500 focus:ring-indigo-500" required>
                        <option value="">Sélectionner...</option>
                        @foreach($employes as $emp)
                            <option value="{{ $emp->id }}" @selected(old('employee_id') == $emp->id)>{{ $emp->nomComplet }}</option>
                        @endforeach
                    </select>
                    <x-input-error :messages="$errors->get('employee_id')" class="mt-2" />
                </div>
                <div>
                    <x-input-label for="libelle" value="Libellé" />
                    <x-text-input id="libelle" name="libelle" class="mt-1 block w-full" required value="{{ old('libelle') }}" placeholder="Ex : Prime de performance" />
                    <x-input-error :messages="$errors->get('libelle')" class="mt-2" />
                </div>
                <div>
                    <x-input-label for="montant" value="Montant (FCFA)" />
                    <x-text-input id="montant" name="montant" type="number" step="0.01" min="0" class="mt-1 block w-full" required value="{{ old('montant', 0) }}" />
                    <x-input-error :messages="$errors->get('montant')" class="mt-2" />
                </div>
                <div>
                    <x-input-label for="date" value="Date" />
                    <x-text-input id="date" name="date" type="date" class="mt-1 block w-full" required value="{{ old('date', now()->toDateString()) }}" />
                    <x-input-error :messages="$errors->get('date')" class="mt-2" />
                </div>
            </div>
        </div>
        <div class="mt-6 flex items-center gap-3">
            <button type="submit" class="btn-primary">Ajouter la prime</button>
            <a href="{{ route('primes.index') }}" class="btn-secondary">Annuler</a>
        </div>
    </form>
</x-app-layout>