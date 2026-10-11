<x-mail::message>
# Configuration SMTP operationnelle

Bonjour,

Cet e-mail confirme que la configuration SMTP de **{{ config('app.name') }}** fonctionne correctement.

Envoye le {{ now()->format('d/m/Y a H:i') }}.

Cordialement,<br>
{{ config('mail.from.name') }}
</x-mail::message>
