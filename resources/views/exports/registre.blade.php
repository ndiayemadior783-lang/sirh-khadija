<!DOCTYPE html>
<html><head><meta charset="utf-8"><style>
body{font-family:sans-serif;font-size:9px;color:#222}
.header{border-bottom:2px solid #1e3a5f;padding-bottom:6px;margin-bottom:8px}
h1{font-size:16px;color:#1e3a5f;text-align:center;margin:0 0 2px}
h2{font-size:12px;text-align:center;color:#333;margin:0 0 4px}
.info{text-align:center;font-size:8.5px;color:#555;margin:0}
table{width:100%;border-collapse:collapse;margin:4px 0}
th,td{border:1px solid #bbb;padding:2px 3px;font-size:7.5px}
th{background:#1e3a5f;color:white;text-transform:uppercase}
.footer{text-align:center;margin-top:12px;font-size:8px;color:#888}
</style></head><body>
<div class="header">
    <h1>RÉGISTRE UNIQUE DU PERSONNEL</h1>
    <h2>{{ $param->nom_entreprise }}</h2>
    <p class="info">Adresse : {{ $param->adresse ?? '—' }} · Tél. : {{ $param->telephone ?? '—' }} · NINEA : {{ $param->nine_a ?? '—' }} · RCCM : {{ $param->rc_cm ?? '—' }} · Effectif : {{ $employes->count() }} employé(s)</p>
</div>
<table>
<tr><th>N°</th><th>Prénoms et Nom</th><th>Date naiss.</th><th>Lieu naiss.</th><th>Sexe</th><th>Nationalité</th><th>N° CNI</th><th>Embauche</th><th>Emploi</th><th>Dépt.</th><th>Salaire</th><th>Adresse</th><th>Statut</th></tr>
@foreach($employes as $i => $e)
<tr>
<td>{{ $i + 1 }}</td>
<td>{{ $e->nomComplet }}</td>
<td>{{ $e->date_naissance?->format('d/m/Y') ?? '—' }}</td>
<td>{{ $e->lieu_naissance ?? '—' }}</td>
<td>{{ $e->genre === 'M' ? 'M' : 'F' }}</td>
<td>{{ $e->nationalite ?? '—' }}</td>
<td>{{ $e->cin ?? '—' }}</td>
<td>{{ $e->date_embauche?->format('d/m/Y') ?? '—' }}</td>
<td>{{ $e->poste?->libelle ?? '—' }}</td>
<td>{{ $e->departement?->nom ?? '—' }}</td>
<td>{{ number_format($e->salaire_base, 0, ',', ' ') }} F</td>
<td>{{ trim(($e->adresse ?? '') . ' ' . ($e->ville ?? '')) ?: '—' }}</td>
<td>{{ $e->statut }}</td>
</tr>
@endforeach
</table>
<p class="footer">Document généré le {{ now()->format('d/m/Y à H:i') }} — {{ $param->nom_entreprise }} · SIRH Khadija Consulting</p>
</body></html>