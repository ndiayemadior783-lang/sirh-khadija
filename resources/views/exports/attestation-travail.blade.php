<!DOCTYPE html>
<html><head><meta charset="utf-8"><style>
body{font-family:sans-serif;font-size:12px;color:#1a1a1a;line-height:1.6}
.header{text-align:center;border-bottom:3px solid #1e3a5f;padding-bottom:10px;margin-bottom:25px}
.header h1{margin:0;color:#1e3a5f;font-size:20px;letter-spacing:1px}
.header p{margin:2px 0;color:#555;font-size:11px}
.entreprise{float:right;text-align:right;font-size:11px;color:#555}
.ref{font-size:11px;color:#777;margin-bottom:20px}
.titre{margin:35px 0;text-align:center}
.titre h2{margin:0;font-size:16px;color:#1e3a5f;border-bottom:2px solid #1e3a5f;display:inline-block;padding:0 20px 5px}
.texte{margin:20px 0;padding:0 10px;text-align:justify}
.info{border:1px solid #ccc;padding:20px;margin:20px auto;width:80%}
.info table{width:100%;border-collapse:collapse}
.info td{padding:6px 10px;font-size:13px;border-bottom:1px solid #eee}
.info td:first-child{font-weight:bold;width:45%}
.signature{margin-top:60px;text-align:right}
.signature .nom{font-weight:bold;margin-top:50px;border-top:1px solid #333;display:inline-block;padding-top:5px}
.footer{margin-top:50px;font-size:10px;color:#888;text-align:center;border-top:1px solid #ddd;padding-top:10px}
</style></head><body>

<div class="header">
    <h1>{{ strtoupper($param->nom_entreprise) }}</h1>
    <p>{{ $param->adresse }}</p>
    @if($param->telephone)<p>Tel : {{ $param->telephone }}</p>@endif
    @if($param->email)<p>{{ $param->email }}</p>@endif
    @if($param->nine_a)<p>NINEA : {{ $param->nine_a }} @if($param->rc_cm) | RCCM : {{ $param->rc_cm }} @endif</p>@endif
</div>

<div class="entreprise">
    <p><strong>Ref :</strong> KC/ATT/{{ now()->format('Y') }}/{{ str_pad((string)$employe->id, 3, '0', STR_PAD_LEFT) }}</p>
    <p>Dakar, le {{ now()->format('d/m/Y') }}</p>
</div>

<div style="clear:both"></div>

<div class="titre">
    <h2>ATTESTATION DE TRAVAIL</h2>
</div>

<div class="texte">
    <p>Je soussigné(e), <strong>{{ $param->nom_entreprise }}</strong>, société dont le siège social est situé à {{ $param->adresse }}
    @if($param->nine_a), immatriculée au registre du commerce sous le numéro {{ $param->rc_cm }} (NINEA : {{ $param->nine_a }})@endif,
    atteste par la présente que :</p>
</div>

<div class="info">
    <table>
        <tr><td>Nom :</td><td>{{ $employe->nom }}</td></tr>
        <tr><td>Prénom(s) :</td><td>{{ $employe->prenom }}</td></tr>
        <tr><td>Matricule :</td><td>{{ $employe->matricule }}</td></tr>
        <tr><td>Poste occupé :</td><td>{{ $employe->poste?->libelle ?? 'Non defini' }}</td></tr>
        <tr><td>Département :</td><td>{{ $employe->departement?->nom ?? 'Non defini' }}</td></tr>
        <tr><td>Date d'embauche :</td><td>{{ $dateEmbauche->format('d/m/Y') }}</td></tr>
        <tr><td>Ancienneté :</td><td>{{ $duree }} mois</td></tr>
        <tr><td>Situation actuelle :</td><td>{{ ucfirst($employe->statut) }}</td></tr>
    </table>
</div>

<div class="texte">
    <p>Cette attestation est délivrée à l'intéressé(e) pour servir et valoir ce que de droit, sur demande de
    @if($employe->genre === 'M') <strong>Monsieur</strong> @else <strong>Madame</strong> @endif {{ $employe->nomComplet }}.</p>
</div>

<div class="signature">
    <p>Fait à {{ $param->adresse }}, le {{ now()->format('d/m/Y') }}</p>
    <div class="nom">LE DIRECTEUR GENERAL</div>
</div>

<div class="footer">
    <p>{{ $param->nom_entreprise }} · Document généré par le SIRH Khadija Consulting le {{ now()->format('d/m/Y H:i') }}</p>
</div>

</body></html>