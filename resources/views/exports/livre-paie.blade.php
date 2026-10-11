<!DOCTYPE html>
<html><head><meta charset="utf-8"><style>
body{font-family:sans-serif;font-size:10px;color:#333}
h1{font-size:16px;color:#1e3a5f;text-align:center}
table{width:100%;border-collapse:collapse;margin:10px 0}
th,td{border:1px solid #ddd;padding:3px 4px;font-size:9px}
th{background:#1e3a5f;color:white;font-size:8px}
.text-right{text-align:right}
.total{font-weight:bold;background:#e8f0fe}
</style></head><body>
<h1>LIVRE DE PAIE - {{ $nomMois }} {{ $annee }}</h1>
<p style="text-align:center">Khadija Consulting - {{ $bulletins->count() }} employe(s)</p>
<table><tr><th>Mat.</th><th>Employe</th><th>Base</th><th>Primes</th><th>Brut</th><th>Cotis.</th><th>Impots</th><th>Net</th></tr>
@foreach($bulletins as $b)<tr>
<td>{{ $b->employee?->matricule }}</td><td>{{ $b->employee?->nomComplet }}</td>
<td class="text-right">{{ number_format($b->salaire_base,0,',',' ') }}</td>
<td class="text-right">{{ number_format($b->primes,0,',',' ') }}</td>
<td class="text-right">{{ number_format($b->salaire_brut,0,',',' ') }}</td>
<td class="text-right">{{ number_format($b->cotisations_sociales,0,',',' ') }}</td>
<td class="text-right">{{ number_format($b->impots,0,',',' ') }}</td>
<td class="text-right"><b>{{ number_format($b->salaire_net,0,',',' ') }}</b></td>
</tr>@endforeach
<tr class="total"><td colspan="2">TOTAUX</td>
<td class="text-right">{{ number_format($totaux['base'],0,',',' ') }}</td>
<td class="text-right">{{ number_format($totaux['primes'],0,',',' ') }}</td>
<td class="text-right">{{ number_format($totaux['brut'],0,',',' ') }}</td>
<td class="text-right">{{ number_format($totaux['cotisations'],0,',',' ') }}</td>
<td class="text-right">{{ number_format($totaux['impots'],0,',',' ') }}</td>
<td class="text-right"><b>{{ number_format($totaux['net'],0,',',' ') }}</b></td></tr>
</table>
</body></html>
