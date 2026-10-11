<x-app-layout>
    <x-slot name="title">Utilisateurs</x-slot>
    <x-slot name="header">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-2xl font-bold text-slate-800">Gestion des utilisateurs</h1>
                <p class="mt-1 text-sm text-slate-500">Comptes admin, RH, manager et employes</p>
            </div>
            <a href="{{ route('utilisateurs.create') }}" class="btn-primary">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Nouvel utilisateur
            </a>
        </div>
    </x-slot>

    @if (session('error'))
        <div class="mb-4 rounded-lg border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-800">{{ session('error') }}</div>
    @endif

    <div class="card overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">Utilisateur</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">Email</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">Role</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">Statut</th>
                        <th class="px-6 py-3 text-right text-xs font-semibold uppercase tracking-wider text-slate-500">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 bg-white">
                    @forelse($users as $u)
                        <tr class="hover:bg-slate-50">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="flex h-9 w-9 items-center justify-center rounded-full bg-indigo-100 text-xs font-bold text-indigo-700">{{ strtoupper(mb_substr($u->name, 0, 2)) }}</div>
                                    <div class="text-sm font-semibold text-slate-800">{{ $u->name }}</div>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-sm text-slate-600">{{ $u->email }}</td>
                            <td class="px-6 py-4">
                                @php
                                    $colors = ['admin' => 'bg-rose-100 text-rose-700', 'rh' => 'bg-indigo-100 text-indigo-700', 'manager' => 'bg-amber-100 text-amber-700', 'employe' => 'bg-emerald-100 text-emerald-700'];
                                @endphp
                                <span class="badge {{ $colors[$u->role] ?? 'bg-slate-100 text-slate-600' }}">{{ $u->libelleRole() }}</span>
                            </td>
                            <td class="px-6 py-4">
                                @if($u->is_active)
                                    <span class="badge bg-emerald-100 text-emerald-700">Actif</span>
                                @else
                                    <span class="badge bg-rose-100 text-rose-700">Inactif</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <button type="button" onclick="document.getElementById('pwd-{{ $u->id }}').classList.toggle('hidden')" class="btn-secondary !px-3 !py-1.5 !text-xs">Mot de passe</button>
                                    <a href="{{ route('utilisateurs.edit', $u) }}" class="btn-secondary !px-3 !py-1.5 !text-xs">Modifier</a>
                                    @if($u->id !== auth()->id())
                                        <form method="POST" action="{{ route('utilisateurs.destroy', $u) }}" onsubmit="return confirm('Supprimer cet utilisateur ?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn-danger !px-3 !py-1.5 !text-xs">Supprimer</button>
                                        </form>
                                    @endif
                                </div>
                                <!-- Reset password -->
                                <div id="pwd-{{ $u->id }}" class="hidden mt-3 rounded-lg border border-slate-200 bg-slate-50 p-3 text-left">
                                    <form method="POST" action="{{ route('utilisateurs.reset-password', $u) }}" class="flex items-end gap-2">
                                        @csrf
                                        <div class="flex-1">
                                            <label class="mb-1 block text-xs font-semibold text-slate-600">Nouveau mot de passe</label>
                                            <input type="password" name="password" required minlength="6" placeholder="Au moins 6 caracteres" class="w-full rounded-lg border border-slate-300 px-3 py-1.5 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                                        </div>
                                        <div class="flex-1">
                                            <label class="mb-1 block text-xs font-semibold text-slate-600">Confirmer</label>
                                            <input type="password" name="password_confirmation" required minlength="6" class="w-full rounded-lg border border-slate-300 px-3 py-1.5 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                                        </div>
                                        <button type="submit" class="btn-primary !px-3 !py-2 !text-xs">Changer</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="px-6 py-10 text-center text-sm text-slate-500">Aucun utilisateur.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>