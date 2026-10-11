<x-app-layout>
    <x-slot name="title">Modifier - {{ $user->name }}</x-slot>
    <x-slot name="header">
        <div>
            <h1 class="text-2xl font-bold text-slate-800">Modifier l utilisateur</h1>
            <p class="mt-1 text-sm text-slate-500">{{ $user->email }}</p>
        </div>
    </x-slot>

    <div class="card max-w-2xl p-6">
        <form method="POST" action="{{ route('utilisateurs.update', $user) }}" class="space-y-4">
            @csrf
            @method('PUT')

            @if ($errors->any())
                <div class="rounded-lg border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-800">
                    <ul class="list-disc pl-4">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
                </div>
            @endif

            <div>
                <label class="mb-1 block text-xs font-semibold text-slate-600">Nom complet *</label>
                <input type="text" name="name" value="{{ old('name', $user->name) }}" required class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-indigo-500 focus:ring-indigo-500">
            </div>
            <div>
                <label class="mb-1 block text-xs font-semibold text-slate-600">Email *</label>
                <input type="email" name="email" value="{{ old('email', $user->email) }}" required class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-indigo-500 focus:ring-indigo-500">
            </div>
            <div>
                <label class="mb-1 block text-xs font-semibold text-slate-600">Role *</label>
                <select name="role" required class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                    <option value="rh" @selected($user->role=='rh')>Gestionnaire RH</option>
                    <option value="manager" @selected($user->role=='manager')>Manager</option>
                    <option value="employe" @selected($user->role=='employe')>Employe</option>
                    <option value="admin" @selected($user->role=='admin')>Administrateur</option>
                </select>
            </div>
            <div>
                <label class="mb-1 block text-xs font-semibold text-slate-600">Telephone</label>
                <input type="text" name="phone" value="{{ old('phone', $user->phone) }}" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-indigo-500 focus:ring-indigo-500">
            </div>
            <label class="flex items-center gap-2 text-sm text-slate-700">
                <input type="checkbox" name="is_active" value="1" @checked($user->is_active) class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                Compte actif
            </label>

            <div class="flex justify-end gap-2 pt-2">
                <a href="{{ route('utilisateurs.index') }}" class="btn-secondary">Annuler</a>
                <button type="submit" class="btn-primary">Enregistrer</button>
            </div>
        </form>
    </div>
</x-app-layout>