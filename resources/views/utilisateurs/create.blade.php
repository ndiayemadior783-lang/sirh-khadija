<x-app-layout>
    <x-slot name="title">Nouvel utilisateur</x-slot>
    <x-slot name="header">
        <div>
            <h1 class="text-2xl font-bold text-slate-800">Nouvel utilisateur</h1>
            <p class="mt-1 text-sm text-slate-500">Creer un compte pour l administration</p>
        </div>
    </x-slot>

    <div class="card max-w-2xl p-6">
        <form method="POST" action="{{ route('utilisateurs.store') }}" class="space-y-4">
            @csrf

            @if ($errors->any())
                <div class="rounded-lg border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-800">
                    <ul class="list-disc pl-4">
                        @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
                    </ul>
                </div>
            @endif

            <div>
                <label class="mb-1 block text-xs font-semibold text-slate-600">Nom complet *</label>
                <input type="text" name="name" value="{{ old('name') }}" required class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-indigo-500 focus:ring-indigo-500">
            </div>
            <div>
                <label class="mb-1 block text-xs font-semibold text-slate-600">Email *</label>
                <input type="email" name="email" value="{{ old('email') }}" required class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-indigo-500 focus:ring-indigo-500">
            </div>
            <div>
                <label class="mb-1 block text-xs font-semibold text-slate-600">Role *</label>
                <select name="role" required class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                    <option value="rh" @selected(old('role')=='rh')>Gestionnaire RH</option>
                    <option value="manager" @selected(old('role')=='manager')>Manager</option>
                    <option value="employe" @selected(old('role')=='employe')>Employe</option>
                    <option value="admin" @selected(old('role')=='admin')>Administrateur</option>
                </select>
            </div>
            <div>
                <label class="mb-1 block text-xs font-semibold text-slate-600">Mot de passe *</label>
                <input type="password" name="password" required minlength="6" placeholder="Au moins 6 caracteres" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-indigo-500 focus:ring-indigo-500">
            </div>
            <div>
                <label class="mb-1 block text-xs font-semibold text-slate-600">Telephone</label>
                <input type="text" name="phone" value="{{ old('phone') }}" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-indigo-500 focus:ring-indigo-500">
            </div>

            <div class="flex justify-end gap-2 pt-2">
                <a href="{{ route('utilisateurs.index') }}" class="btn-secondary">Annuler</a>
                <button type="submit" class="btn-primary">Creer l utilisateur</button>
            </div>
        </form>
    </div>
</x-app-layout>