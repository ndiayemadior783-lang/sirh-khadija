<x-mail::message>
# Bulletin de paie - {{ $bulletin->period }}

Bonjour {{ $bulletin->employee?->prenom }},

Veuillez trouver ci-joint votre bulletin de paie **n° {{ $bulletin->numero }}** pour la periode **{{ $bulletin->period }}**.

Salaire net : **{{ number_format((float) $bulletin->salaire_net, 0, ',', ' ') }} FCFA**

Ce document est confidentiel.

Cordialement,<br>
{{ config('mail.from.name') }}
</x-mail::message>
