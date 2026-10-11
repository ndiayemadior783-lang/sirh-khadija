<x-app-layout>
    <x-slot name="title">Clients</x-slot>
    <x-slot name="header">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-2xl font-bold text-slate-800">Gestion des clients</h1>
                <p class="mt-1 text-sm text-slate-500">Portefeuille clients & partenaires de Khadija Consulting</p>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('factures.index') }}" class="btn-secondary">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    Voir les factures
                </a>
                <a href="{{ route('clients.create') }}" class="btn-primary">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    Nouveau client
                </a>
            </div>
        </div>
    </x-slot>

    <!-- Filtre & Recherche -->
    <form method="GET" action="{{ route('clients.index') }}" class="card mb-6 p-4">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center">
            <div class="flex-1">
                <input type="text" name="search" value="{{ request('search') }}"
                       placeholder="Rechercher par nom, contact, NINEA, téléphone..."
                       class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-indigo-500 focus:ring-indigo-500">
            </div>
            <div class="flex gap-2">
                <button type="submit" class="btn-secondary">Rechercher</button>
                @if(request('search'))
                    <a href="{{ route('clients.index') }}" class="btn-secondary">Réinitialiser</a>
                @endif
            </div>
        </div>
    </form>

    <!-- Tableau des clients -->
    <div class="card overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">Client / Entreprise</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">Contact</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">Coordonnées</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">NINEA / RC</th>
                        <th class="px-6 py-3 text-center text-xs font-semibold uppercase tracking-wider text-slate-500">Factures</th>
                        <th class="px-6 py-3 text-right text-xs font-semibold uppercase tracking-wider text-slate-500">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 bg-white">
                    @forelse($clients as $client)
                        <tr class="hover:bg-slate-50">
                            <td class="px-6 py-4">
                                <div class="font-semibold text-slate-900">{{ $client->nom }}</div>
                                <div class="text-xs text-slate-500">{{ $client->ville ?: 'Dakar' }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm text-slate-800">{{ $client->contact_nom ?: '—' }}</div>
                            </td>
                            <td class="px-6 py-4 text-sm text-slate-600">
                                @if($client->email)
                                    <div><a href="mailto:{{ $client->email }}" class="text-indigo-600 hover:underline">{{ $client->email }}</a></div>
                                @endif
                                @if($client->telephone)
                                    <div class="text-xs text-slate-500">{{ $client->telephone }}</div>
                                @endif
                                @if(!$client->email && !$client->telephone)
                                    <span class="text-slate-400">—</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-sm text-slate-600">
                                @if($client->ninea)
                                    <div><span class="font-mono text-xs font-semibold text-slate-700">NINEA:</span> {{ $client->ninea }}</div>
                                @endif
                                @if($client->rc_cm)
                                    <div class="text-xs text-slate-500"><span class="font-mono text-xs font-semibold text-slate-700">RC:</span> {{ $client->rc_cm }}</div>
                                @endif
                                @if(!$client->ninea && !$client->rc_cm)
                                    <span class="text-slate-400">—</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-center">
                                <a href="{{ route('factures.index', ['client_id' => $client->id]) }}"
                                   class="inline-flex items-center rounded-full bg-slate-100 px-2.5 py-0.5 text-xs font-medium text-slate-800 hover:bg-slate-200">
                                    {{ $client->factures_count }} facture(s)
                                </a>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('clients.edit', $client) }}" class="rounded p-1 text-slate-500 hover:bg-slate-100 hover:text-indigo-600" title="Modifier">
                                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                    </a>
                                    <form method="POST" action="{{ route('clients.destroy', $client) }}" onsubmit="return confirm('Supprimer ce client ?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="rounded p-1 text-slate-500 hover:bg-slate-100 hover:text-rose-600" title="Supprimer">
                                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-8 text-center text-sm text-slate-500">
                                Aucun client trouvé. Cliquez sur « Nouveau client » pour commencer.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($clients->hasPages())
            <div class="border-t border-slate-200 p-4">
                {{ $clients->links() }}
            </div>
        @endif
    </div>
</x-app-layout>
