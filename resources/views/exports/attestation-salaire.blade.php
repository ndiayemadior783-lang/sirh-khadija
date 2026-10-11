<!DOCTYPE html>
<html><head><meta charset="utf-8"><style>
body{font-family:sans-serif;font-size:12px;color:#1a1a1a;line-height:1.6}
.header{text-align:center;border-bottom:3px solid #1e3a5f;padding-bottom:10px;margin-bottom:25px}
.header h1{margin:0;color:#1e3a5f;font-size:20px;letter-spacing:1px}
.header p{margin:2px 0;color:#555;font-size:11px}
.entreprise{float:right;text-align:right;font-size:11px;color:#555}
.titre{margin:35px 0;text-align:center}
.titre h2{margin:0;font-size:16px;color:#1e3a5f;border-bottom:2px solid #1e3a5f;display:inline-block;padding:0 20px 5px}
.texte{margin:20px 0;padding:0 10px;text-align:justify}
.info{border:1px solid #ccc;padding:15px;margin:20px auto;width:90%}
.info table{width:100%;border-collapse:collapse}
.info td{padding:6px 8px;font-size:12px;border-bottom:1px solid #eee}
.info td:first-child{font-weight:bold;width:50%}
.brut{font-weight:bold;font-size:13px;background:#f0f4ff;padding:8px}
.net{font-weight:bold;font-size:15px;background:#e8f5e9;padding:8px;text-align:center;color:#2e7d32}
.signature{margin-top:60px;text-align:right}
.signature .nom{font-weight:bold;margin-top:50px;border-top:1px solid #333;display:inline-block;padding-top:5px}
.footer{margin-top:50px;font-size:10px;color:#888;text-align:center;border-top:1px solid #ddd;padding-top:10px}
</style></head><body>

<div class="header">
    <h1>{{ strtoupper($param->nom_entreprise) }}</h1>
    <p>{{ $param->adresse }}</p>
    @if($param->telephone)<p>Tel : {{ $param->telephone }}</p>@endif
    @if($param->nine_a)<p>NINEA : {{ $param->nine_a }} @if($param->rc_cm) | RCCM : {{ $param->rc_cm }} @endif</p>@endif
</div>

<div class="entreprise">
    <p><strong>Ref :</strong> KC/ATT/SAL/{{ now()->format('Y') }}/{{ str_pad((string)$employe->id, 3, '0', STR_PAD_LEFT) }}</p>
    <p>Dakar, le {{ now()->format('d/m/Y') }}</p>
</div>

<div style="clear:both"></div>

<div class="titre">
    <h2>ATTESTATION DE SALAIRE</h2>
</div>

<div class="texte">
    <p>Je soussigné(e), <strong>{{ $param->nom_entreprise }}</strong>, atteste que :</p>
</div>

<div class="info">
    <table>
        <tr><td>Agent :</td><td>{{ $employe->nomComplet }}</td></tr>
        <tr><td>Matricule :</td><td>{{ $employe->matricule }}</td></tr>
        <tr><td>Poste :</td><td>{{ $employe->poste?->libelle ?? 'Non defini' }}</td></tr>
        <tr><td>Département :</td><td>{{ $employe->departement?->nom ?? 'Non defini' }}</td></tr>
        <tr><td>Date d'embauche :</td><td>{{ $employe->date_embauche ? \Carbon\Carbon::parse($employe->date_embauche)->format('d/m/Y') : 'Non defini' }}</td></tr>
    </table>
</div>

@if($dernierBulletin)
<div class="texte">
    <p>Sous reserve des accesses et retenues legales, percevait pour la periode <strong>{{ $dernierBulletin->period }}</strong> les sommement suivantes :</p>
</div>

<div class="info">
    <table>
        <tr><td>Salaire de base :</td><td>{{ number_format($dernierBulletin->salaire_base, 0, ',', ' ') }} FCFA</td></tr>
        @if($dernierBulletin->primes > 0)<tr><td>Primes :</td><td>{{ number_format($dernierBulletin->primes, 0, ',', ' ') }} FCFA</td></tr>@endif
        @if($dernierBulletin->heures_sup > 0)<tr><td>Heures supplementaires :</td><td>{{ number_format($dernierBulletin->heures_sup, 0, ',', ' ') }} FCFA</td></tr>@endif
        @if($dernierBulletin->indemnites > 0)<tr><td>Indemnites :</td><td>{{ number_format($dernierBulletin->indemnites, 0, ',', ' ') }} FCFA</td></tr>@endif
        <tr class="brut"><td>Salaire brut :</td><td>{{ number_format($dernierBulletin->salaire_brut, 0, ',', ' ') }} FCFA</td></tr>
        <tr><td>Cotisations sociales :</td><td>- {{ number_format($dernierBulletin->cotisations_sociales, 0, ',', ' ') }} FCFA</td></tr>
        <tr><td>Impot sur le revenu :</td><td>- {{ number_format($dernierBulletin->impots, 0, ',', ' ') }} FCFA</td></tr>
        <tr class="net"><td>SALAIRE NET :</td><td class="net">{{ number_format($dernierBulletin->salaire_net, 0, ',', ' ') }} FCFA</td></tr>
    </table>
</div>
@else
<div class="texte">
    <p><em>Aucun bulletin de paie disponible pour cet employe.</em></p>
</div>
@endif

<div class="texte">
    <p>La presente attestation est delivree a l'interesse(e) pour servir et valoir ce que de droit.</p>
</div>

<div class="signature">
    <p>Fait a {{ $param->adresse }}, le {{ now()->format('d/m/Y') }}</p>
    <div class="nom">LE DIRECTEUR GENERAL</div>
</div>

<div class="footer">
    <p>{{ $param->nom_entreprise }} · Document genere par le SIRH Khadija Consulting le {{ now()->format('d/m/Y H:i') }}</p>
</div>

</body></html>