<x-app-layout>
    <x-slot name="title">Nouvelle formation</x-slot>
    <x-slot name="header">
        <h1 class="text-2xl font-bold text-slate-800">Créer une formation</h1>
    </x-slot>

    <form method="POST" action="{{ route('formations.store') }}" class="max-w-2xl">
        @csrf
        <div class="card p-6">
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                <div class="sm:col-span-2">
                    <x-input-label for="titre" value="Titre de la formation" />
                    <x-text-input id="titre" name="titre" class="mt-1 block w-full" required value="{{ old('titre') }}" />
                    <x-input-error :messages="$errors->get('titre')" class="mt-2" />
                </div>
                <div>
                    <x-input-label for="date_debut" value="Date de début" />
                    <x-text-input id="date_debut" name="date_debut" type="date" class="mt-1 block w-full" value="{{ old('date_debut') }}" />
                </div>
                <div>
                    <x-input-label for="date_fin" value="Date de fin" />
                    <x-text-input id="date_fin" name="date_fin" type="date" class="mt-1 block w-full" value="{{ old('date_fin') }}" />
                    <x-input-error :messages="$errors->get('date_fin')" class="mt-2" />
                </div>
                <div>
                    <x-input-label for="formateur" value="Formateur" />
                    <x-text-input id="formateur" name="formateur" class="mt-1 block w-full" value="{{ old('formateur') }}" />
                </div>
                <div>
                    <x-input-label for="lieu" value="Lieu" />
                    <x-text-input id="lieu" name="lieu" class="mt-1 block w-full" value="{{ old('lieu', 'Dakar') }}" />
                </div>
                <div class="sm:col-span-2">
                    <x-input-label for="cout" value="Coût (FCFA)" />
                    <x-text-input id="cout" name="cout" type="number" step="0.01" min="0" class="mt-1 block w-full" required value="{{ old('cout', 0) }}" />
                    <x-input-error :messages="$errors->get('cout')" class="mt-2" />
                </div>
                <div class="sm:col-span-2">
                    <x-input-label for="description" value="Description" />
                    <textarea id="description" name="description" rows="3" class="mt-1 block w-full rounded-lg border-slate-300 focus:border-indigo-500 focus:ring-indigo-500">{{ old('description') }}</textarea>
                </div>
            </div>
        </div>
        <div class="mt-6 flex items-center gap-3">
            <button type="submit" class="btn-primary">Créer la formation</button>
            <a href="{{ route('formations.index') }}" class="btn-secondary">Annuler</a>
        </div>
    </form>
</x-app-layout>