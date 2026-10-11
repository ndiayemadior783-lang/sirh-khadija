<!DOCTYPE html>
<html><head><meta charset="utf-8"><style>
body{font-family:sans-serif;font-size:9px;color:#222}
h1{font-size:15px;color:#1e3a5f;text-align:center;margin:0 0 2px}
.entete{text-align:center;font-size:9px;color:#555;margin:0 0 4px}
.legend{text-align:center;font-size:8px;color:#555;margin-bottom:8px}
.legend span{display:inline-block;margin:0 6px}
.badge{display:inline-block;width:8px;height:8px;border-radius:50%;margin-right:3px}
.footer{text-align:center;margin-top:10px;font-size:8px;color:#888}
</style></head><body>
<h1>PLANNING DES CONGÉS — {{ strtoupper($nomMois) }} {{ $annee }}</h1>
<p class="entete">{{ $societe }} · Validés : {{ $statsValides }} · En attente : {{ $statsAttente }} · Refusés : {{ $statsRefuses }}</p>
<div class="legend">
    <span><span class="badge" style="background:#10b981"></span>Validé</span>
    <span><span class="badge" style="background:#f59e0b"></span>En attente</span>
    <span><span class="badge" style="background:#f43f5e"></span>Refusé</span>
</div>
@include('conges.partials.calendar-grid', ['compact' => true])
<p class="footer">Document généré le {{ now()->format('d/m/Y à H:i') }} — {{ $societe }} · SIRH Khadija Consulting</p>
</body></html>