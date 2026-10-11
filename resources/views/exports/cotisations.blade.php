<!DOCTYPE html>
<html><head><meta charset="utf-8"><style>
body{font-family:sans-serif;font-size:9px;color:#333}
h1{font-size:16px;color:#1e3a5f;text-align:center}
table{width:100%;border-collapse:collapse;margin:10px 0}
th,td{border:1px solid #ddd;padding:2px 3px;font-size:8px}
th{background:#1e3a5f;color:white;font-size:8px}
.text-right{text-align:right}
.total{font-weight:bold;background:#e8f0fe}
</style></head><body>
<h1>Etat des Cotisations - {{ $nomMois }} {{ $annee }}</h1>
<p style="text-align:center">Khadija Consulting - {{ $bulletins->count() }} employe(s)</p>
<table><tr><th>Employe</th><th>Brut</th><th>IPRES Empl.</th><th>IPRES Sal.</th><th>IPM Empl.</th><th>IPM Sal.</th><th>CFRP Empl.</th><th>CFRP Sal.</th><th>Taxe 3%</th><th>Total</th><th>I.R.</th><th>Net</th></tr>
@foreach($bulletins as $b)
@php $brut=$b->salaire_brut; @endphp
<tr>
<td>{{ $b->employee?->nomComplet }}</td>
<td class="text-right">{{ number_format($brut,0,',',' ') }}</td>
<td class="text-right">{{ number_format($brut*0.084,0,',',' ') }}</td>
<td class="text-right">{{ number_format($brut*0.056,0,',',' ') }}</td>
<td class="text-right">{{ number_format($brut*0.0325,0,',',' ') }}</td>
<td class="text-right">{{ number_format($brut*0.0125,0,',',' ') }}</td>
<td class="text-right">{{ number_format($brut*0.06,0,',',' ') }}</td>
<td class="text-right">{{ number_format($brut*0.06,0,',',' ') }}</td>
<td class="text-right">{{ number_format($brut*0.03,0,',',' ') }}</td>
<td class="text-right"><b>{{ number_format($b->cotisations_sociales + $b->impots,0,',',' ') }}</b></td>
<td class="text-right">{{ number_format($b->impots,0,',',' ') }}</td>
<td class="text-right"><b>{{ number_format($b->salaire_net,0,',',' ') }}</b></td>
</tr>@endforeach
</table>
</body></html>
