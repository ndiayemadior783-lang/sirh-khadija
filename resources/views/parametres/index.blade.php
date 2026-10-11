<x-app-layout>
    <x-slot name="title">Parametres</x-slot>
    <x-slot name="header">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-2xl font-bold text-slate-800">Parametres du systeme</h1>
                <p class="mt-1 text-sm text-slate-500">Configuration de l entreprise et des taux</p>
            </div>
        </div>
    </x-slot>

    <form method="POST" action="{{ route('parametres.update') }}" class="space-y-6">
        @csrf

        @if ($errors->any())
            <div class="rounded-lg border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-800">
                <ul class="list-disc pl-4">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Entreprise -->
        <div class="card p-6">
            <h2 class="mb-4 text-base font-bold text-slate-800">Informations de l entreprise</h2>
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                <div>
                    <label class="mb-1 block text-xs font-semibold text-slate-600">Nom de l entreprise *</label>
                    <input type="text" name="nom_entreprise" value="{{ $param->nom_entreprise }}" required class="focus:ring-indigo-500 focus:border-indigo-500 block w-full rounded-lg border-slate-300 shadow-sm sm:text-sm">
                </div>
                <div>
                    <label class="mb-1 block text-xs font-semibold text-slate-600">Adresse</label>
                    <input type="text" name="adresse" value="{{ $param->adresse }}" class="focus:ring-indigo-500 focus:border-indigo-500 block w-full rounded-lg border-slate-300 shadow-sm sm:text-sm">
                </div>
                <div>
                    <label class="mb-1 block text-xs font-semibold text-slate-600">Telephone</label>
                    <input type="text" name="telephone" value="{{ $param->telephone }}" class="focus:ring-indigo-500 focus:border-indigo-500 block w-full rounded-lg border-slate-300 shadow-sm sm:text-sm">
                </div>
                <div>
                    <label class="mb-1 block text-xs font-semibold text-slate-600">Email</label>
                    <input type="email" name="email" value="{{ $param->email }}" class="focus:ring-indigo-500 focus:border-indigo-500 block w-full rounded-lg border-slate-300 shadow-sm sm:text-sm">
                </div>
                <div>
                    <label class="mb-1 block text-xs font-semibold text-slate-600">NINEA</label>
                    <input type="text" name="nine_a" value="{{ $param->nine_a }}" class="focus:ring-indigo-500 focus:border-indigo-500 block w-full rounded-lg border-slate-300 shadow-sm sm:text-sm">
                </div>
                <div>
                    <label class="mb-1 block text-xs font-semibold text-slate-600">RCCM</label>
                    <input type="text" name="rc_cm" value="{{ $param->rc_cm }}" class="focus:ring-indigo-500 focus:border-indigo-500 block w-full rounded-lg border-slate-300 shadow-sm sm:text-sm">
                </div>
            </div>
        </div>

        <!-- Taux de cotisations -->
        <div class="card p-6">
            <h2 class="mb-1 text-base font-bold text-slate-800">Taux de cotisations sociales</h2>
            <p class="mb-4 text-xs text-slate-500">En pourcentage (ex : 0.056 = 5.6%)</p>
            <div class="grid grid-cols-2 gap-4 sm:grid-cols-4">
                <div>
                    <label class="mb-1 block text-xs font-semibold text-slate-600">IPRES employeur</label>
                    <input type="number" step="0.0001" name="ipres_employeur" value="{{ $param->ipres_employeur }}" class="focus:ring-indigo-500 focus:border-indigo-500 block w-full rounded-lg border-slate-300 shadow-sm sm:text-sm">
                </div>
                <div>
                    <label class="mb-1 block text-xs font-semibold text-slate-600">IPRES employe</label>
                    <input type="number" step="0.0001" name="ipres_employe" value="{{ $param->ipres_employe }}" class="focus:ring-indigo-500 focus:border-indigo-500 block w-full rounded-lg border-slate-300 shadow-sm sm:text-sm">
                </div>
                <div>
                    <label class="mb-1 block text-xs font-semibold text-slate-600">IPM employeur</label>
                    <input type="number" step="0.0001" name="ipm_employeur" value="{{ $param->ipm_employeur }}" class="focus:ring-indigo-500 focus:border-indigo-500 block w-full rounded-lg border-slate-300 shadow-sm sm:text-sm">
                </div>
                <div>
                    <label class="mb-1 block text-xs font-semibold text-slate-600">IPM employe</label>
                    <input type="number" step="0.0001" name="ipm_employe" value="{{ $param->ipm_employe }}" class="focus:ring-indigo-500 focus:border-indigo-500 block w-full rounded-lg border-slate-300 shadow-sm sm:text-sm">
                </div>
                <div>
                    <label class="mb-1 block text-xs font-semibold text-slate-600">CFRP employeur</label>
                    <input type="number" step="0.0001" name="cfrp_employeur" value="{{ $param->cfrp_employeur }}" class="focus:ring-indigo-500 focus:border-indigo-500 block w-full rounded-lg border-slate-300 shadow-sm sm:text-sm">
                </div>
                <div>
                    <label class="mb-1 block text-xs font-semibold text-slate-600">CFRP employe</label>
                    <input type="number" step="0.0001" name="cfrp_employe" value="{{ $param->cfrp_employe }}" class="focus:ring-indigo-500 focus:border-indigo-500 block w-full rounded-lg border-slate-300 shadow-sm sm:text-sm">
                </div>
                <div>
                    <label class="mb-1 block text-xs font-semibold text-slate-600">Taxe exceptionnelle</label>
                    <input type="number" step="0.0001" name="taxe_exceptionnelle" value="{{ $param->taxe_exceptionnelle }}" class="focus:ring-indigo-500 focus:border-indigo-500 block w-full rounded-lg border-slate-300 shadow-sm sm:text-sm">
                </div>
            </div>
        </div>

        <!-- Paie -->
        <div class="card p-6">
            <h2 class="mb-4 text-base font-bold text-slate-800">Parametres de paie</h2>
            <div class="grid grid-cols-2 gap-4 sm:grid-cols-4">
                <div>
                    <label class="mb-1 block text-xs font-semibold text-slate-600">Taux cotisation globale</label>
                    <input type="number" step="0.0001" name="taux_cotisation" value="{{ $param->taux_cotisation }}" class="focus:ring-indigo-500 focus:border-indigo-500 block w-full rounded-lg border-slate-300 shadow-sm sm:text-sm">
                </div>
                <div>
                    <label class="mb-1 block text-xs font-semibold text-slate-600">Taux impot</label>
                    <input type="number" step="0.0001" name="taux_impot" value="{{ $param->taux_impot }}" class="focus:ring-indigo-500 focus:border-indigo-500 block w-full rounded-lg border-slate-300 shadow-sm sm:text-sm">
                </div>
                <div>
                    <label class="mb-1 block text-xs font-semibold text-slate-600">Taux horaire (FCFA)</label>
                    <input type="number" step="0.01" name="taux_horaire" value="{{ $param->taux_horaire }}" class="focus:ring-indigo-500 focus:border-indigo-500 block w-full rounded-lg border-slate-300 shadow-sm sm:text-sm">
                </div>
                <div>
                    <label class="mb-1 block text-xs font-semibold text-slate-600">Jours de conge paye / an</label>
                    <input type="number" name="jours_conge_paye" value="{{ $param->jours_conge_paye }}" class="focus:ring-indigo-500 focus:border-indigo-500 block w-full rounded-lg border-slate-300 shadow-sm sm:text-sm">
                </div>
            </div>
        </div>

        <!-- Messagerie SMTP -->
        <div class="card p-6">
            <div class="mb-4 flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h2 class="text-base font-bold text-slate-800">Messagerie (SMTP)</h2>
                    <p class="text-xs text-slate-500">Serveur utilise pour l envoi des e-mails (conges, bulletins, mot de passe oublie)</p>
                </div>
                <label class="inline-flex items-center gap-2 text-sm font-semibold text-slate-700">
                    <input type="hidden" name="mail_actif" value="0">
                    <input type="checkbox" name="mail_actif" value="1" @checked(old('mail_actif', $param->mail_actif)) class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                    Activer l envoi SMTP
                </label>
            </div>
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-4">
                <div class="sm:col-span-2">
                    <label class="mb-1 block text-xs font-semibold text-slate-600">Serveur SMTP</label>
                    <input type="text" name="mail_host" value="{{ old('mail_host', $param->mail_host) }}" placeholder="smtp.gmail.com" class="focus:ring-indigo-500 focus:border-indigo-500 block w-full rounded-lg border-slate-300 shadow-sm sm:text-sm">
                </div>
                <div>
                    <label class="mb-1 block text-xs font-semibold text-slate-600">Port</label>
                    <input type="number" name="mail_port" value="{{ old('mail_port', $param->mail_port ?? 587) }}" class="focus:ring-indigo-500 focus:border-indigo-500 block w-full rounded-lg border-slate-300 shadow-sm sm:text-sm">
                </div>
                <div>
                    <label class="mb-1 block text-xs font-semibold text-slate-600">Chiffrement</label>
                    <select name="mail_encryption" class="focus:ring-indigo-500 focus:border-indigo-500 block w-full rounded-lg border-slate-300 shadow-sm sm:text-sm">
                        @php($enc = old('mail_encryption', $param->mail_encryption))
                        <option value="tls" @selected($enc === 'tls')>TLS (port 587)</option>
                        <option value="ssl" @selected($enc === 'ssl')>SSL (port 465)</option>
                        <option value="" @selected($enc === null || $enc === '')>Aucun</option>
                    </select>
                </div>
                <div class="sm:col-span-2">
                    <label class="mb-1 block text-xs font-semibold text-slate-600">Nom d utilisateur</label>
                    <input type="text" name="mail_username" value="{{ old('mail_username', $param->mail_username) }}" autocomplete="off" class="focus:ring-indigo-500 focus:border-indigo-500 block w-full rounded-lg border-slate-300 shadow-sm sm:text-sm">
                </div>
                <div class="sm:col-span-2">
                    <label class="mb-1 block text-xs font-semibold text-slate-600">Mot de passe</label>
                    <input type="password" name="mail_password" value="" autocomplete="new-password" placeholder="{{ $param->mail_password ? '•••••••• (laisser vide pour conserver)' : '' }}" class="focus:ring-indigo-500 focus:border-indigo-500 block w-full rounded-lg border-slate-300 shadow-sm sm:text-sm">
                    @if($param->mail_password)
                        <label class="mt-1 inline-flex items-center gap-1 text-xs text-slate-500">
                            <input type="checkbox" name="mail_password_clear" value="1" class="rounded border-slate-300 text-rose-600 focus:ring-rose-500"> Effacer le mot de passe enregistre
                        </label>
                    @endif
                </div>
                <div class="sm:col-span-2">
                    <label class="mb-1 block text-xs font-semibold text-slate-600">Adresse d expedition</label>
                    <input type="email" name="mail_from_address" value="{{ old('mail_from_address', $param->mail_from_address) }}" placeholder="rh@entreprise.sn" class="focus:ring-indigo-500 focus:border-indigo-500 block w-full rounded-lg border-slate-300 shadow-sm sm:text-sm">
                </div>
                <div class="sm:col-span-2">
                    <label class="mb-1 block text-xs font-semibold text-slate-600">Nom d expediteur</label>
                    <input type="text" name="mail_from_name" value="{{ old('mail_from_name', $param->mail_from_name) }}" placeholder="{{ $param->nom_entreprise }}" class="focus:ring-indigo-500 focus:border-indigo-500 block w-full rounded-lg border-slate-300 shadow-sm sm:text-sm">
                </div>
                <div class="sm:col-span-4">
                    <label class="inline-flex items-center gap-2 text-sm text-slate-700">
                        <input type="hidden" name="mail_notif_conges" value="0">
                        <input type="checkbox" name="mail_notif_conges" value="1" @checked(old('mail_notif_conges', $param->mail_notif_conges ?? true)) class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                        Notifier automatiquement l employe par e-mail lors de la validation / du refus d un conge
                    </label>
                </div>
            </div>
        </div>

        <div class="flex justify-end">
            <button type="submit" class="btn-primary">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                Enregistrer les parametres
            </button>
        </div>
    </form>

    <!-- Test SMTP -->
    <form method="POST" action="{{ route('parametres.test-smtp') }}" class="card mt-6 p-6">
        @csrf
        <h2 class="mb-1 text-base font-bold text-slate-800">Tester la configuration SMTP</h2>
        <p class="mb-4 text-xs text-slate-500">Enregistrez d abord les parametres, puis envoyez un e-mail de test (envoi immediat, hors file d attente).</p>
        <div class="flex flex-col gap-3 sm:flex-row">
            <input type="email" name="destinataire" required value="{{ old('destinataire', auth()->user()->email) }}" class="focus:ring-indigo-500 focus:border-indigo-500 block w-full rounded-lg border-slate-300 shadow-sm sm:text-sm sm:max-w-sm">
            <button type="submit" class="btn-secondary">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                Envoyer un e-mail de test
            </button>
        </div>
    </form>

    <!-- File d attente des e-mails -->
    <div class="card mt-6 p-6">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="mb-1 text-base font-bold text-slate-800">File d attente des e-mails</h2>
                <p class="text-xs text-slate-500">Les notifications de conges et les bulletins sont envoyes en arriere-plan (connexion : {{ $fileAttente['connexion'] }}).</p>
            </div>
            <div class="flex items-center gap-3 text-sm">
                <span class="rounded-lg bg-slate-100 px-3 py-1.5 font-semibold text-slate-700">{{ $fileAttente['en_attente'] }} en attente</span>
                <span class="rounded-lg px-3 py-1.5 font-semibold {{ $fileAttente['echecs'] ? 'bg-rose-100 text-rose-700' : 'bg-emerald-100 text-emerald-700' }}">{{ $fileAttente['echecs'] }} en echec</span>
                @if($fileAttente['echecs'])
                    <form method="POST" action="{{ route('parametres.relancer-emails') }}">
                        @csrf
                        <button type="submit" class="btn-secondary">Relancer les echecs</button>
                    </form>
                @endif
            </div>
        </div>
        @if($fileAttente['dernier_echec'])
            <p class="mt-3 rounded-lg bg-rose-50 px-3 py-2 text-xs text-rose-800 break-words">
                Derniere erreur : {{ $fileAttente['dernier_echec'] }}
            </p>
        @endif
        @if($fileAttente['connexion'] !== 'sync' && $fileAttente['en_attente'] > 0)
            <p class="mt-3 text-xs text-amber-700">Si ce nombre ne diminue pas, verifiez que le worker tourne : <code>php artisan queue:work</code></p>
        @endif
    </div>
</x-app-layout>