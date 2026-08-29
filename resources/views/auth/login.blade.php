<x-guest-layout>
    <div class="mb-6 text-center">
        <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-indigo-600 text-2xl font-bold text-white shadow-lg">
            K
        </div>
        <h2 class="mt-4 text-2xl font-bold text-slate-800">Khadija Consulting</h2>
        <p class="mt-1 text-sm text-slate-500">Système d'Information des Ressources Humaines</p>
    </div>

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <div>
            <x-input-label for="email" value="Adresse email" />
            <x-text-input id="email" class="mt-1 block w-full" type="email" name="email" :value="old('email')" placeholder="admin@khadijaconsulting.sn" required autofocus autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div class="mt-4">
            <x-input-label for="password" value="Mot de passe" />
            <x-text-input id="password" class="mt-1 block w-full" type="password" name="password" required autocomplete="current-password" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <div class="mt-4">
            <button type="submit" class="w-full rounded-lg bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white shadow transition hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                Se connecter
            </button>
        </div>
    </form>

    <p class="mt-6 text-center text-xs text-slate-400">
        Comptes de démonstration : admin@khadijaconsulting.sn · rh@khadijaconsulting.sn · manager@khadijaconsulting.sn<br>Mot de passe : password
    </p>
</x-guest-layout>