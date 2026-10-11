<x-mail::message>
# Demande de conge {{ $conge->statut === 'valide' ? 'validee' : 'refusee' }}

Bonjour {{ $conge->employee?->prenom }},

Votre demande de conge a ete **{{ $conge->statut === 'valide' ? 'validee' : 'refusee' }}**.

<x-mail::table>
| Detail | Valeur |
|:-------|:-------|
| Type | {{ ucfirst($conge->type) }} |
| Du | {{ $conge->date_debut?->format('d/m/Y') }} |
| Au | {{ $conge->date_fin?->format('d/m/Y') }} |
| Nombre de jours | {{ $conge->jours }} |
@if($conge->valideur)
| Traitee par | {{ $conge->valideur->name }} |
@endif
</x-mail::table>

<x-mail::button :url="route('conges.index')">
Voir mes conges
</x-mail::button>

Cordialement,<br>
{{ config('mail.from.name') }}
</x-mail::message>
