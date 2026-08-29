<x-app-layout>
    <x-slot name="title">Modifier {{ $employe->nomComplet }}</x-slot>
    <x-slot name="header">
        <div>
            <h1 class="text-2xl font-bold text-slate-800">Modifier l'employé</h1>
            <p class="mt-1 text-sm text-slate-500">{{ $employe->nomComplet }} · {{ $employe->matricule }}</p>
        </div>
    </x-slot>

    <form method="POST" action="{{ route('employes.update', $employe) }}" class="max-w-4xl">
        @csrf
        @method('PUT')

        <div class="card divide-y divide-slate-100">
            <div class="p-6">
                <h2 class="mb-4 text-base font-semibold text-slate-800">Identité</h2>
                <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
                    <div>
                        <x-input-label for="prenom" value="Prénom" />
                        <x-text-input id="prenom" name="prenom" class="mt-1 block w-full" required value="{{ old('prenom', $employe->prenom) }}" />
                    </div>
                    <div>
                        <x-input-label for="nom" value="Nom" />
                        <x-text-input id="nom" name="nom" class="mt-1 block w-full" required value="{{ old('nom', $employe->nom) }}" />
                    </div>
                    <div>
                        <x-input-label for="genre" value="Genre" />
                        <select id="genre" name="genre" class="mt-1 block w-full rounded-lg border-slate-300 focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="M" @selected(old('genre', $employe->genre) == 'M')>Masculin</option>
                            <option value="F" @selected(old('genre', $employe->genre) == 'F')>Féminin</option>
                        </select>
                    </div>
                    <div>
                        <x-input-label for="date_naissance" value="Date de naissance" />
                        <x-text-input id="date_naissance" name="date_naissance" type="date" class="mt-1 block w-full" value="{{ old('date_naissance', $employe->date_naissance?->format('Y-m-d')) }}" />
                    </div>
                    <div>
                        <x-input-label for="cin" value="N° pièce d'identité" />
                        <x-text-input id="cin" name="cin" class="mt-1 block w-full" value="{{ old('cin', $employe->cin) }}" />
                    </div>
                    <div>
                        <x-input-label for="nationalite" value="Nationalité" />
                        <x-text-input id="nationalite" name="nationalite" class="mt-1 block w-full" value="{{ old('nationalite', $employe->nationalite) }}" />
                    </div>
                </div>
            </div>

            <div class="p-6">
                <h2 class="mb-4 text-base font-semibold text-slate-800">Coordonnées</h2>
                <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
                    <div>
                        <x-input-label for="email" value="Email" />
                        <x-text-input id="email" name="email" type="email" class="mt-1 block w-full" value="{{ old('email', $employe->email) }}" />
                        <x-input-error :messages="$errors->get('email')" class="mt-2" />
                    </div>
                    <div>
                        <x-input-label for="telephone" value="Téléphone" />
                        <x-text-input id="telephone" name="telephone" class="mt-1 block w-full" value="{{ old('telephone', $employe->telephone) }}" />
                    </div>
                    <div>
                        <x-input-label for="ville" value="Ville" />
                        <x-text-input id="ville" name="ville" class="mt-1 block w-full" value="{{ old('ville', $employe->ville) }}" />
                    </div>
                    <div class="md:col-span-3">
                        <x-input-label for="adresse" value="Adresse" />
                        <x-text-input id="adresse" name="adresse" class="mt-1 block w-full" value="{{ old('adresse', $employe->adresse) }}" />
                    </div>
                </div>
            </div>

            <div class="p-6">
                <h2 class="mb-4 text-base font-semibold text-slate-800">Emploi</h2>
                <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
                    <div>
                        <x-input-label for="departement_id" value="Département" />
                        <select id="departement_id" name="departement_id" class="mt-1 block w-full rounded-lg border-slate-300 focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="">Sélectionner...</option>
                            @foreach($departements as $dep)
                                <option value="{{ $dep->id }}" @selected(old('departement_id', $employe->departement_id) == $dep->id)>{{ $dep->nom }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <x-input-label for="poste_id" value="Poste" />
                        <select id="poste_id" name="poste_id" class="mt-1 block w-full rounded-lg border-slate-300 focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="">Sélectionner...</option>
                            @foreach($postes as $poste)
                                <option value="{{ $poste->id }}" @selected(old('poste_id', $employe->poste_id) == $poste->id)>{{ $poste->libelle }} ({{ $poste->departement?->nom }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <x-input-label for="responsable_id" value="Responsable" />
                        <select id="responsable_id" name="responsable_id" class="mt-1 block w-full rounded-lg border-slate-300 focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="">Aucun</option>
                            @foreach($responsables as $resp)
                                <option value="{{ $resp->id }}" @selected(old('responsable_id', $employe->responsable_id) == $resp->id)>{{ $resp->nomComplet }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <x-input-label for="date_embauche" value="Date d'embauche" />
                        <x-text-input id="date_embauche" name="date_embauche" type="date" class="mt-1 block w-full" required value="{{ old('date_embauche', $employe->date_embauche->format('Y-m-d')) }}" />
                    </div>
                    <div>
                        <x-input-label for="statut" value="Statut" />
                        <select id="statut" name="statut" class="mt-1 block w-full rounded-lg border-slate-300 focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="actif" @selected(old('statut', $employe->statut) == 'actif')>Actif</option>
                            <option value="inactif" @selected(old('statut') == 'inactif')>Inactif</option>
                            <option value="suspendu" @selected(old('statut') == 'suspendu')>Suspendu</option>
                            <option value="demission" @selected(old('statut') == 'demission')>Démission</option>
                        </select>
                    </div>
                    <div>
                        <x-input-label for="salaire_base" value="Salaire de base (FCFA)" />
                        <x-text-input id="salaire_base" name="salaire_base" type="number" step="0.01" min="0" class="mt-1 block w-full" required value="{{ old('salaire_base', $employe->salaire_base) }}" />
                    </div>
                </div>
            </div>
        </div>

        <div class="mt-6 flex items-center gap-3">
            <button type="submit" class="btn-primary">Enregistrer les modifications</button>
            <a href="{{ route('employes.show', $employe) }}" class="btn-secondary">Annuler</a>
        </div>
    </form>
</x-app-layout>