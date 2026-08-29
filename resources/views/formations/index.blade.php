<x-app-layout>
    <x-slot name="title">Formations</x-slot>
    <x-slot name="header">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-2xl font-bold text-slate-800">Plan de formation</h1>
                <p class="mt-1 text-sm text-slate-500">Catalogue des formations du personnel</p>
            </div>
            <a href="{{ route('formations.create') }}" class="btn-primary">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Nouvelle formation
            </a>
        </div>
    </x-slot>

    <div class="grid grid-cols-1 gap-6 md:grid-cols-2 xl:grid-cols-3">
        @forelse($formations as $formation)
            <a href="{{ route('formations.show', $formation) }}" class="card p-6 transition hover:shadow-md">
                <div class="flex items-center justify-between">
                    <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-indigo-50 text-indigo-600">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                    </div>
                    <span class="badge bg-indigo-100 text-indigo-700">{{ $formation->employes_count }} inscrit(s)</span>
                </div>
                <h2 class="mt-4 text-base font-semibold text-slate-800">{{ $formation->titre }}</h2>
                <p class="mt-1 line-clamp-2 text-sm text-slate-500">{{ $formation->description }}</p>
                <div class="mt-4 grid grid-cols-2 gap-2 border-t border-slate-100 pt-4 text-xs text-slate-500">
                    <span>Du {{ $formation->date_debut?->format('d/m/Y') ?? '—' }}</span>
                    <span>au {{ $formation->date_fin?->format('d/m/Y') ?? '—' }}</span>
                    <span class="truncate">Formateur : {{ $formation->formateur ?? '—' }}</span>
                    <span class="font-semibold text-slate-700">{{ number_format($formation->cout, 0, ',', ' ') }} F</span>
                </div>
            </a>
        @empty
            <p class="col-span-full py-10 text-center text-sm text-slate-500">Aucune formation.</p>
        @endforelse
    </div>
</x-app-layout>