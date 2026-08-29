<x-app-layout>
    <x-slot name="title">Nouveau département</x-slot>
    <x-slot name="header">
        <h1 class="text-2xl font-bold text-slate-800">Créer un département</h1>
    </x-slot>

    <form method="POST" action="{{ route('departements.store') }}" class="max-w-xl">
        @csrf
        <div class="card p-6">
            <div class="grid grid-cols-1 gap-4">
                <div>
                    <x-input-label for="nom" value="Nom du département" />
                    <x-text-input id="nom" name="nom" class="mt-1 block w-full" required value="{{ old('nom') }}" placeholder="Ex : Ressources Humaines" />
                    <x-input-error :messages="$errors->get('nom')" class="mt-2" />
                </div>
                <div>
                    <x-input-label for="responsable" value="Responsable" />
                    <x-text-input id="responsable" name="responsable" class="mt-1 block w-full" value="{{ old('responsable') }}" placeholder="Nom du responsable" />
                </div>
                <div>
                    <x-input-label for="description" value="Description" />
                    <textarea id="description" name="description" rows="3" class="mt-1 block w-full rounded-lg border-slate-300 focus:border-indigo-500 focus:ring-indigo-500">{{ old('description') }}</textarea>
                    <x-input-error :messages="$errors->get('description')" class="mt-2" />
                </div>
            </div>
        </div>
        <div class="mt-6 flex items-center gap-3">
            <button type="submit" class="btn-primary">Créer</button>
            <a href="{{ route('departements.index') }}" class="btn-secondary">Annuler</a>
        </div>
    </form>
</x-app-layout>