@php
    $user = auth()->user();
@endphp

<!-- Sidebar -->
<aside class="fixed inset-y-0 left-0 z-40 hidden w-64 flex-col bg-slate-900 lg:flex">
    <div class="flex h-16 items-center gap-3 border-b border-white/10 px-6">
        <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-indigo-600 text-lg font-bold text-white">
            K
        </div>
        <div>
            <div class="text-sm font-bold text-white">Khadija Consulting</div>
            <div class="text-xs text-slate-400">SIRH</div>
        </div>
    </div>

    <nav class="sidebar-scroll flex-1 overflow-y-auto px-4 py-4">
        <div class="sidebar-section">Navigation</div>
        <a href="{{ route('dashboard') }}" class="sidebar-link @if(request()->routeIs('dashboard')) sidebar-link-active @endif">
            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l9-9 9 9M5 10v10a1 1 0 001 1h3a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1h3a1 1 0 001-1V10M9 21h6"/></svg>
            Tableau de bord
        </a>

        @if($user->estGestion())
        <div class="sidebar-section">Ressources humaines</div>
        <a href="{{ route('employes.index') }}" class="sidebar-link @if(request()->routeIs('employes.*')) sidebar-link-active @endif">
            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a4 4 0 00-3-3.87M9 20H4v-2a4 4 0 013-3.87m6-1.13a4 4 0 10-4-4 4 4 0 004 4zm6 1.13a3 3 0 10-3-3 3 3 0 003 3zM9 12a4 4 0 01-4-4v-2a4 4 0 018 0v2a4 4 0 01-4 4z"/></svg>
            Employés
        </a>
        @if($user->estRH())
        <a href="{{ route('contrats.index') }}" class="sidebar-link @if(request()->routeIs('contrats.*')) sidebar-link-active @endif">
            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
            Contrats
        </a>
        <a href="{{ route('departements.index') }}" class="sidebar-link @if(request()->routeIs('departements.*')) sidebar-link-active @endif">
            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
            Départements
        </a>
        @endif
        @endif

        @if($user->estGestion())
        <div class="sidebar-section">Gestion des temps</div>
        <a href="{{ route('presences.index') }}" class="sidebar-link @if(request()->routeIs('presences.*')) sidebar-link-active @endif">
            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            Présences & Pointage
        </a>
        @endif
        <a href="{{ route('conges.index') }}" class="sidebar-link @if(request()->routeIs('conges.*')) sidebar-link-active @endif">
            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
            Congés & Absences
        </a>

        @if($user->estGestion())
        <div class="sidebar-section">Formation</div>
        <a href="{{ route('formations.index') }}" class="sidebar-link @if(request()->routeIs('formations.*')) sidebar-link-active @endif">
            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
            Formations
        </a>
        @endif

        @if($user->estRH())
        <div class="sidebar-section">Paie & salaires</div>
        <a href="{{ route('bulletins.index') }}" class="sidebar-link @if(request()->routeIs('bulletins.*')) sidebar-link-active @endif">
            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
            Bulletins de paie
        </a>
        <a href="{{ route('primes.index') }}" class="sidebar-link @if(request()->routeIs('primes.*')) sidebar-link-active @endif">
            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            Primes & Indemnités
        </a>
        @endif
    </nav>

    <div class="border-t border-white/10 p-4">
        <div class="flex items-center gap-3">
            <div class="flex h-9 w-9 items-center justify-center rounded-full bg-indigo-500 text-sm font-bold text-white">
                {{ strtoupper(mb_substr($user->name, 0, 2)) }}
            </div>
            <div class="min-w-0 flex-1">
                <div class="truncate text-sm font-semibold text-white">{{ $user->name }}</div>
                <div class="text-xs text-slate-400">{{ $user->libelleRole() }}</div>
            </div>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button class="text-slate-400 transition hover:text-white" title="Déconnexion">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                </button>
            </form>
        </div>
    </div>
</aside>

<!-- Mobile header -->
<div class="lg:hidden">
    <div class="flex h-16 items-center justify-between bg-slate-900 px-4">
        <div class="flex items-center gap-2">
            <div class="flex h-9 w-9 items-center justify-center rounded-xl bg-indigo-600 text-lg font-bold text-white">K</div>
            <span class="text-sm font-bold text-white">Khadija Consulting</span>
        </div>
        <div class="flex items-center gap-3">
            <span class="text-xs text-slate-300">{{ $user->name }}</span>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button class="text-slate-300 hover:text-white">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                </button>
            </form>
        </div>
    </div>
</div>