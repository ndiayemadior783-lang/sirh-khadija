<!DOCTYPE html>
<html><head><meta charset="utf-8"><style>
body{font-family:sans-serif;font-size:10px;color:#333}
h1{font-size:16px;color:#1e3a5f;text-align:center}
p{text-align:center;font-size:10px;color:#555}
table{width:100%;border-collapse:collapse;margin:10px 0}
th,td{border:1px solid #ddd;padding:4px 5px;font-size:9px}
th{background:#1e3a5f;color:white}
</style></head><body>
<h1>Liste des attestations et certificats</h1>
<p>Khadija Consulting - {{ $employes->count() }} employé(s)</p>
<table><tr><th>Mat.</th><th>Nom complet</th><th>Poste</th><th>Département</th><th>Embauche</th><th>Statut</th></tr>
@foreach($employes as $e)<tr>
<td>{{ $e->matricule }}</td><td>{{ $e->nomComplet }}</td>
<td>{{ $e->poste?->libelle }}</td><td>{{ $e->departement?->nom }}</td>
<td>{{ $e->date_embauche?->format('d/m/Y') }}</td><td>{{ $e->statut }}</td>
</tr>@endforeach
</table>
<p>Documents disponibles pour chaque employé : attestation de travail, attestation de salaire, certificat de travail.</p>
</body></html>