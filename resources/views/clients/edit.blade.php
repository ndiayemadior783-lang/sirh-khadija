<x-app-layout>
    <x-slot name="title">Modifier client</x-slot>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-slate-800">Modifier le client : {{ $client->nom }}</h1>
                <p class="mt-1 text-sm text-slate-500">Mettre à jour les coordonnées et identifiants fiscaux</p>
            </div>
            <a href="{{ route('clients.index') }}" class="btn-secondary">
                Retour à la liste
            </a>
        </div>
    </x-slot>

    <div class="max-w-3xl">
        <form method="POST" action="{{ route('clients.update', $client) }}" class="card p-6">
            @csrf
            @method('PUT')

            <div class="space-y-6">
                <!-- Informations générales -->
                <div>
                    <h2 class="text-base font-semibold text-slate-900 border-b border-slate-200 pb-2 mb-4">Informations de l'entreprise</h2>
                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                        <div class="sm:col-span-2">
                            <label class="block text-xs font-semibold text-slate-700 uppercase mb-1">Raison sociale / Nom du client *</label>
                            <input type="text" name="nom" value="{{ old('nom', $client->nom) }}" required
                                   class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-indigo-500 focus:ring-indigo-500 @error('nom') border-rose-500 @enderror">
                            @error('nom') <p class="mt-1 text-xs text-rose-500">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-slate-700 uppercase mb-1">Nom du contact / Responsable</label>
                            <input type="text" name="contact_nom" value="{{ old('contact_nom', $client->contact_nom) }}"
                                   class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-slate-700 uppercase mb-1">Email</label>
                            <input type="email" name="email" value="{{ old('email', $client->email) }}"
                                   class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-indigo-500 focus:ring-indigo-500 @error('email') border-rose-500 @enderror">
                            @error('email') <p class="mt-1 text-xs text-rose-500">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-slate-700 uppercase mb-1">Téléphone</label>
                            <input type="text" name="telephone" value="{{ old('telephone', $client->telephone) }}"
                                   class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-slate-700 uppercase mb-1">Ville</label>
                            <input type="text" name="ville" value="{{ old('ville', $client->ville) }}"
                                   class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                        </div>

                        <div class="sm:col-span-2">
                            <label class="block text-xs font-semibold text-slate-700 uppercase mb-1">Adresse géographique</label>
                            <input type="text" name="adresse" value="{{ old('adresse', $client->adresse) }}"
                                   class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                        </div>
                    </div>
                </div>

                <!-- Identifiants fiscaux sénégalais -->
                <div>
                    <h2 class="text-base font-semibold text-slate-900 border-b border-slate-200 pb-2 mb-4">Identifiants légaux & fiscaux (Sénégal)</h2>
                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 uppercase mb-1">NINEA</label>
                            <input type="text" name="ninea" value="{{ old('ninea', $client->ninea) }}"
                                   class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-slate-700 uppercase mb-1">RC / RCCM</label>
                            <input type="text" name="rc_cm" value="{{ old('rc_cm', $client->rc_cm) }}"
                                   class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                        </div>
                    </div>
                </div>

                <!-- Notes internes -->
                <div>
                    <label class="block text-xs font-semibold text-slate-700 uppercase mb-1">Notes / Modalités particulières</label>
                    <textarea name="notes" rows="3"
                              class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('notes', $client->notes) }}</textarea>
                </div>
            </div>

            <div class="mt-6 flex justify-end gap-3 border-t border-slate-200 pt-4">
                <a href="{{ route('clients.index') }}" class="btn-secondary">Annuler</a>
                <button type="submit" class="btn-primary">Mettre à jour</button>
            </div>
        </form>
    </div>
</x-app-layout>
