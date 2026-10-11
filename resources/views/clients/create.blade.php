<x-app-layout>
    <x-slot name="title">Nouveau client</x-slot>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-slate-800">Ajouter un nouveau client</h1>
                <p class="mt-1 text-sm text-slate-500">Enregistrer les coordonnées et identifiants fiscaux du client</p>
            </div>
            <a href="{{ route('clients.index') }}" class="btn-secondary">
                Retour à la liste
            </a>
        </div>
    </x-slot>

    <div class="max-w-3xl">
        <form method="POST" action="{{ route('clients.store') }}" class="card p-6">
            @csrf

            <div class="space-y-6">
                <!-- Informations générales -->
                <div>
                    <h2 class="text-base font-semibold text-slate-900 border-b border-slate-200 pb-2 mb-4">Informations de l'entreprise</h2>
                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                        <div class="sm:col-span-2">
                            <label class="block text-xs font-semibold text-slate-700 uppercase mb-1">Raison sociale / Nom du client *</label>
                            <input type="text" name="nom" value="{{ old('nom') }}" required placeholder="Ex: Sonatel SA, Banque Atlantique..."
                                   class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-indigo-500 focus:ring-indigo-500 @error('nom') border-rose-500 @enderror">
                            @error('nom') <p class="mt-1 text-xs text-rose-500">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-slate-700 uppercase mb-1">Nom du contact / Responsable</label>
                            <input type="text" name="contact_nom" value="{{ old('contact_nom') }}" placeholder="Ex: M. Ousmane Diop"
                                   class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-slate-700 uppercase mb-1">Email</label>
                            <input type="email" name="email" value="{{ old('email') }}" placeholder="contact@client.sn"
                                   class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-indigo-500 focus:ring-indigo-500 @error('email') border-rose-500 @enderror">
                            @error('email') <p class="mt-1 text-xs text-rose-500">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-slate-700 uppercase mb-1">Téléphone</label>
                            <input type="text" name="telephone" value="{{ old('telephone') }}" placeholder="+221 33 800 00 00"
                                   class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-slate-700 uppercase mb-1">Ville</label>
                            <input type="text" name="ville" value="{{ old('ville', 'Dakar') }}" placeholder="Dakar"
                                   class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                        </div>

                        <div class="sm:col-span-2">
                            <label class="block text-xs font-semibold text-slate-700 uppercase mb-1">Adresse géographique</label>
                            <input type="text" name="adresse" value="{{ old('adresse') }}" placeholder="Ex: Almadies, Route de l'Aéroport, Dakar"
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
                            <input type="text" name="ninea" value="{{ old('ninea') }}" placeholder="Ex: 00458792 2V2"
                                   class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-slate-700 uppercase mb-1">RC / RCCM</label>
                            <input type="text" name="rc_cm" value="{{ old('rc_cm') }}" placeholder="Ex: SN-DKR-2022-B-1234"
                                   class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                        </div>
                    </div>
                </div>

                <!-- Notes internes -->
                <div>
                    <label class="block text-xs font-semibold text-slate-700 uppercase mb-1">Notes / Modalités particulières</label>
                    <textarea name="notes" rows="3" placeholder="Informations internes, conditions négociées..."
                              class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('notes') }}</textarea>
                </div>
            </div>

            <div class="mt-6 flex justify-end gap-3 border-t border-slate-200 pt-4">
                <a href="{{ route('clients.index') }}" class="btn-secondary">Annuler</a>
                <button type="submit" class="btn-primary">Enregistrer le client</button>
            </div>
        </form>
    </div>
</x-app-layout>
