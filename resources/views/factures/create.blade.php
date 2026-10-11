<x-app-layout>
    <x-slot name="title">Nouvelle facture</x-slot>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-slate-800">Établir une nouvelle facture</h1>
                <p class="mt-1 text-sm text-slate-500">Facturation des honoraires & prestations de conseil Khadija Consulting</p>
            </div>
            <a href="{{ route('factures.index') }}" class="btn-secondary">
                Retour à la liste
            </a>
        </div>
    </x-slot>

    <div x-data="factureForm()" class="max-w-5xl">
        <form method="POST" action="{{ route('factures.store') }}" class="card p-6">
            @csrf

            <!-- Informations principales -->
            <div class="mb-8 border-b border-slate-200 pb-6">
                <h2 class="text-base font-semibold text-slate-900 mb-4">Informations générales</h2>
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
                    <div>
                        <label class="block text-xs font-semibold uppercase text-slate-700 mb-1">Numéro de facture *</label>
                        <input type="text" name="numero" value="{{ old('numero', $numeroPropose) }}" required
                               class="w-full font-mono rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-indigo-500 focus:ring-indigo-500 @error('numero') border-rose-500 @enderror">
                        @error('numero') <p class="mt-1 text-xs text-rose-500">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-semibold uppercase text-slate-700 mb-1">Client *</label>
                        <select name="client_id" required class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-indigo-500 focus:ring-indigo-500 @error('client_id') border-rose-500 @enderror">
                            <option value="">Sélectionner un client...</option>
                            @foreach($clients as $c)
                                <option value="{{ $c->id }}" @selected(old('client_id') == $c->id)>{{ $c->nom }} ({{ $c->ville ?: 'Dakar' }})</option>
                            @endforeach
                        </select>
                        @error('client_id') <p class="mt-1 text-xs text-rose-500">{{ $message }}</p> @enderror
                        @if($clients->isEmpty())
                            <p class="mt-1 text-xs text-amber-600">Aucun client. <a href="{{ route('clients.create') }}" class="underline font-semibold">Créer un client d'abord</a>.</p>
                        @endif
                    </div>

                    <div>
                        <label class="block text-xs font-semibold uppercase text-slate-700 mb-1">Statut initial *</label>
                        <select name="statut" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="brouillon" @selected(old('statut') == 'brouillon')>Brouillon</option>
                            <option value="emise" @selected(old('statut') == 'emise')>Émise / Envoyée</option>
                            <option value="payee" @selected(old('statut') == 'payee')>Payée d'avance</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold uppercase text-slate-700 mb-1">Date d'émission *</label>
                        <input type="date" name="date_facture" value="{{ old('date_facture', now()->toDateString()) }}" required
                               class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold uppercase text-slate-700 mb-1">Date d'échéance</label>
                        <input type="date" name="date_echeance" value="{{ old('date_echeance', now()->addDays(30)->toDateString()) }}"
                               class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold uppercase text-slate-700 mb-1">Mode de règlement prévu</label>
                        <select name="mode_paiement" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="">Non défini</option>
                            <option value="virement" @selected(old('mode_paiement') == 'virement')>Virement bancaire</option>
                            <option value="cheque" @selected(old('mode_paiement') == 'cheque')>Chèque</option>
                            <option value="especes" @selected(old('mode_paiement') == 'especes')>Espèces</option>
                            <option value="wave" @selected(old('mode_paiement') == 'wave')>Wave</option>
                            <option value="orange_money" @selected(old('mode_paiement') == 'orange_money')>Orange Money</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Prestations / Lignes de facture -->
            <div class="mb-8">
                <div class="flex items-center justify-between mb-3">
                    <h2 class="text-base font-semibold text-slate-900">Prestations & Lignes de facture</h2>
                    <button type="button" @click="ajouterLigne()" class="btn-secondary text-xs">
                        <svg class="h-4 w-4 mr-1 inline" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        Ajouter une ligne
                    </button>
                </div>

                <div class="overflow-x-auto rounded-lg border border-slate-200">
                    <table class="min-w-full divide-y divide-slate-200 text-sm">
                        <thead class="bg-slate-50">
                            <tr>
                                <th class="px-4 py-3 text-left font-semibold text-slate-600">Désignation / Mission</th>
                                <th class="w-24 px-4 py-3 text-center font-semibold text-slate-600">Quantité</th>
                                <th class="w-40 px-4 py-3 text-right font-semibold text-slate-600">Prix unitaire (FCFA)</th>
                                <th class="w-44 px-4 py-3 text-right font-semibold text-slate-600">Total HT (FCFA)</th>
                                <th class="w-12 px-2 py-3 text-center"></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 bg-white">
                            <template x-for="(ligne, index) in lignes" :key="index">
                                <tr>
                                    <td class="p-2">
                                        <input type="text" :name="'lignes[' + index + '][designation]'" x-model="ligne.designation"
                                               required placeholder="Ex: Mission d'audit RH, Formation 3 jours, Conseil stratégique..."
                                               class="w-full rounded border border-slate-300 px-2 py-1.5 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    </td>
                                    <td class="p-2">
                                        <input type="number" step="0.5" min="0.5" :name="'lignes[' + index + '][quantite]'" x-model.number="ligne.quantite"
                                               required class="w-full rounded border border-slate-300 px-2 py-1.5 text-center text-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    </td>
                                    <td class="p-2">
                                        <input type="number" step="1000" min="0" :name="'lignes[' + index + '][prix_unitaire]'" x-model.number="ligne.prix_unitaire"
                                               required class="w-full rounded border border-slate-300 px-2 py-1.5 text-right font-mono text-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    </td>
                                    <td class="p-2 text-right font-mono font-medium text-slate-800">
                                        <span x-text="formatFCFA(ligne.quantite * ligne.prix_unitaire)"></span>
                                    </td>
                                    <td class="p-2 text-center">
                                        <button type="button" @click="supprimerLigne(index)" :disabled="lignes.length <= 1"
                                                class="text-slate-400 hover:text-rose-600 disabled:opacity-25" title="Supprimer">
                                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        </button>
                                    </td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>

                <!-- Récapitulatif financier & TVA -->
                <div class="mt-4 flex flex-col sm:flex-row justify-between items-start gap-6">
                    <div class="w-full sm:w-1/2 space-y-3">
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" name="appliquer_tva" value="1" x-model="appliquerTva"
                                   class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                            <span class="text-sm font-semibold text-slate-700">Appliquer la TVA sénégalaise (18%)</span>
                        </label>
                        <p class="text-xs text-slate-500">
                            Décocher si la prestation est exonérée de TVA ou soumise à un régime spécifique d'exportation de services.
                        </p>
                    </div>

                    <div class="w-full sm:w-80 rounded-lg bg-slate-50 p-4 border border-slate-200">
                        <div class="flex justify-between py-1 text-sm text-slate-600">
                            <span>Total Hors Taxes (HT) :</span>
                            <span class="font-mono font-semibold text-slate-900" x-text="formatFCFA(totalHT()) + ' FCFA'"></span>
                        </div>
                        <div class="flex justify-between py-1 text-sm text-slate-600" x-show="appliquerTva">
                            <span>TVA (18%) :</span>
                            <span class="font-mono font-semibold text-slate-900" x-text="formatFCFA(montantTVA()) + ' FCFA'"></span>
                        </div>
                        <div class="mt-2 flex justify-between border-t border-slate-300 pt-2 text-base font-bold text-slate-900">
                            <span>Total TTC à payer :</span>
                            <span class="font-mono text-indigo-600" x-text="formatFCFA(totalTTC()) + ' FCFA'"></span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Conditions & Notes -->
            <div class="mb-8 border-t border-slate-200 pt-6">
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <div>
                        <label class="block text-xs font-semibold uppercase text-slate-700 mb-1">Conditions de règlement</label>
                        <textarea name="conditions_reglement" rows="3"
                                  class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('conditions_reglement', "Règlement par virement bancaire ou chèque à l'ordre de Khadija Consulting sous 30 jours à réception de facture.") }}</textarea>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold uppercase text-slate-700 mb-1">Notes / Remarques additionnelles</label>
                        <textarea name="notes" rows="3" placeholder="Informations destinées au client ou références de bon de commande..."
                                  class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('notes') }}</textarea>
                    </div>
                </div>
            </div>

            <div class="flex justify-end gap-3 border-t border-slate-200 pt-4">
                <a href="{{ route('factures.index') }}" class="btn-secondary">Annuler</a>
                <button type="submit" class="btn-primary">Créer et enregistrer la facture</button>
            </div>
        </form>
    </div>

    <script>
        function factureForm() {
            return {
                appliquerTva: true,
                lignes: [
                    { designation: '', quantite: 1, prix_unitaire: 250000 }
                ],
                ajouterLigne() {
                    this.lignes.push({ designation: '', quantite: 1, prix_unitaire: 0 });
                },
                supprimerLigne(index) {
                    if (this.lignes.length > 1) {
                        this.lignes.splice(index, 1);
                    }
                },
                totalHT() {
                    return this.lignes.reduce((sum, ligne) => {
                        return sum + ((parseFloat(ligne.quantite) || 0) * (parseFloat(ligne.prix_unitaire) || 0));
                    }, 0);
                },
                montantTVA() {
                    return this.appliquerTva ? Math.round(this.totalHT() * 0.18) : 0;
                },
                totalTTC() {
                    return this.totalHT() + this.montantTVA();
                },
                formatFCFA(val) {
                    return new Intl.NumberFormat('fr-FR').format(Math.round(val || 0));
                }
            }
        }
    </script>
</x-app-layout>
