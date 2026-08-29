@php $user = auth()->user(); @endphp
<x-app-layout>
    <x-slot name="title">Nouvelle demande de congé</x-slot>
    <x-slot name="header">
        <h1 class="text-2xl font-bold text-slate-800">Demander un congé</h1>
    </x-slot>

    <form method="POST" action="{{ route('conges.store') }}" class="max-w-2xl">
        @csrf
        <div class="card p-6">
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                @if($user->estGestion())
                <div class="sm:col-span-2">
                    <x-input-label for="employee_id" value="Employé" />
                    <select id="employee_id" name="employee_id" class="mt-1 block w-full rounded-lg border-slate-300 focus:border-indigo-500 focus:ring-indigo-500" required>
                        <option value="">Sélectionner...</option>
                        @foreach($employes as $emp)
                            <option value="{{ $emp->id }}" @selected(old('employee_id') == $emp->id)>{{ $emp->nomComplet }}</option>
                        @endforeach
                    </select>
                </div>
                @endif
                <div>
                    <x-input-label for="type" value="Type de congé" />
                    <select id="type" name="type" class="mt-1 block w-full rounded-lg border-slate-300 focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="paye">Congé payé</option>
                        <option value="maladie">Congé maladie</option>
                        <option value="maternite">Congé maternité</option>
                        <option value="sans_solde">Congé sans solde</option>
                        <option value="exceptionnel">Congé exceptionnel</option>
                    </select>
                </div>
                <div></div>
                <div>
                    <x-input-label for="date_debut" value="Date de début" />
                    <x-text-input id="date_debut" name="date_debut" type="date" class="mt-1 block w-full" required value="{{ old('date_debut', now()->toDateString()) }}" />
                    <x-input-error :messages="$errors->get('date_debut')" class="mt-2" />
                </div>
                <div>
                    <x-input-label for="date_fin" value="Date de fin" />
                    <x-text-input id="date_fin" name="date_fin" type="date" class="mt-1 block w-full" required value="{{ old('date_fin') }}" />
                    <x-input-error :messages="$errors->get('date_fin')" class="mt-2" />
                </div>
                <div class="sm:col-span-2">
                    <x-input-label for="motif" value="Motif" />
                    <textarea id="motif" name="motif" rows="3" class="mt-1 block w-full rounded-lg border-slate-300 focus:border-indigo-500 focus:ring-indigo-500">{{ old('motif') }}</textarea>
                </div>
            </div>
        </div>
        <div class="mt-6 flex items-center gap-3">
            <button type="submit" class="btn-primary">Soumettre la demande</button>
            <a href="{{ route('conges.index') }}" class="btn-secondary">Annuler</a>
        </div>
    </form>
</x-app-layout>