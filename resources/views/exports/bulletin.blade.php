<!DOCTYPE html>
<html><head><meta charset="utf-8"><style>
body{font-family:sans-serif;font-size:12px;color:#333}
h1{font-size:18px;color:#1e3a5f;border-bottom:2px solid #1e3a5f;padding-bottom:5px}
table{width:100%;border-collapse:collapse;margin:15px 0}
th,td{border:1px solid #ddd;padding:6px 8px;text-align:left;font-size:11px}
th{background:#1e3a5f;color:white}
.text-right{text-align:right}
.total{font-weight:bold;background:#e8f0fe}
.header{display:flex;justify-content:space-between;margin-bottom:20px}
</style></head><body>
<div class="header"><div><h1>KHADIJA CONSULTING</h1><p>Cabinet de Conseil RH - Dakar, Senegal</p></div>
<div style="text-align:right"><h2>Bulletin de Paie</h2><p>N : {{ $bulletin->numero }}</p><p>Periode : {{ $bulletin->period }}</p></div></div>
<table>
<tr><th>Employe</th><td>{{ $bulletin->employee?->nomComplet }} ({{ $bulletin->employee?->matricule }})</td><th>Poste</th><td>{{ $bulletin->employee?->poste?->libelle }}</td></tr>
<tr><th>Departement</th><td>{{ $bulletin->employee?->departement?->nom }}</td><th>Date emission</th><td>{{ now()->format('d/m/Y') }}</td></tr>
</table>
<h3>Salaire</h3>
<table><tr><th>Element</th><th class="text-right">Montant (FCFA)</th></tr>
<tr><td>Salaire de base</td><td class="text-right">{{ number_format($bulletin->salaire_base, 0, ',', ' ') }}</td></tr>
<tr><td>Primes</td><td class="text-right">+ {{ number_format($bulletin->primes, 0, ',', ' ') }}</td></tr>
<tr><td>Heures supplementaires</td><td class="text-right">+ {{ number_format($bulletin->heures_sup, 0, ',', ' ') }}</td></tr>
<tr><td>Indemnites</td><td class="text-right">+ {{ number_format($bulletin->indemnites, 0, ',', ' ') }}</td></tr>
<tr class="total"><td>Salaire brut</td><td class="text-right">{{ number_format($bulletin->salaire_brut, 0, ',', ' ') }}</td></tr>
<tr><td>Cotisations sociales</td><td class="text-right">- {{ number_format($bulletin->cotisations_sociales, 0, ',', ' ') }}</td></tr>
<tr><td>Impot sur le revenu</td><td class="text-right">- {{ number_format($bulletin->impots, 0, ',', ' ') }}</td></tr>
<tr class="total"><td>NET A PAYER</td><td class="text-right" style="color:#1e3a5f">{{ number_format($bulletin->salaire_net, 0, ',', ' ') }} FCFA</td></tr>
</table>
<p style="text-align:center;color:#888;font-size:10px;margin-top:30px">Document genere par le SIRH de Khadija Consulting - {{ now()->format('d/m/Y H:i') }}</p>
</body></html>
