<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Facture {{ $facture->numero }}</title>
    <style>
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            font-size: 12px;
            color: #222;
            line-height: 1.5;
            margin: 0;
            padding: 15px;
        }
        .header {
            border-bottom: 2px solid #1e3a5f;
            padding-bottom: 15px;
            margin-bottom: 20px;
        }
        .header table {
            width: 100%;
            border-collapse: collapse;
        }
        .company-name {
            font-size: 18px;
            font-weight: bold;
            color: #1e3a5f;
            margin: 0;
        }
        .company-desc {
            font-size: 10px;
            color: #555;
            margin: 2px 0 6px 0;
        }
        .company-meta {
            font-size: 10px;
            color: #444;
            line-height: 1.4;
        }
        .facture-box {
            text-align: right;
        }
        .facture-title {
            font-size: 20px;
            font-weight: bold;
            color: #1e3a5f;
            margin: 0;
            letter-spacing: 1px;
        }
        .facture-num {
            font-size: 13px;
            font-family: monospace;
            font-weight: bold;
            color: #333;
            margin-top: 4px;
        }
        .facture-dates {
            font-size: 10px;
            color: #555;
            margin-top: 6px;
        }
        .client-box {
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 4px;
            padding: 12px 15px;
            margin-bottom: 25px;
        }
        .client-box table {
            width: 100%;
        }
        .client-title {
            font-size: 10px;
            font-weight: bold;
            text-transform: uppercase;
            color: #64748b;
            margin-bottom: 4px;
        }
        .client-name {
            font-size: 14px;
            font-weight: bold;
            color: #0f172a;
        }
        .client-details {
            font-size: 11px;
            color: #334155;
            line-height: 1.4;
        }
        .lignes-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .lignes-table th {
            background-color: #1e3a5f;
            color: #ffffff;
            font-size: 10px;
            font-weight: bold;
            text-transform: uppercase;
            padding: 8px 10px;
            text-align: left;
        }
        .lignes-table th.text-center { text-align: center; }
        .lignes-table th.text-right { text-align: right; }
        .lignes-table td {
            padding: 10px;
            border-bottom: 1px solid #e2e8f0;
            font-size: 11px;
        }
        .lignes-table td.text-center { text-align: center; }
        .lignes-table td.text-right { text-align: right; font-family: monospace; }
        .totals-section {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }
        .totals-table {
            width: 280px;
            float: right;
            border-collapse: collapse;
        }
        .totals-table td {
            padding: 6px 10px;
            font-size: 11px;
        }
        .totals-table .total-label {
            color: #475569;
            text-align: left;
        }
        .totals-table .total-value {
            text-align: right;
            font-family: monospace;
            font-weight: bold;
            color: #0f172a;
        }
        .totals-table .grand-total td {
            border-top: 2px solid #1e3a5f;
            font-size: 13px;
            font-weight: bold;
            color: #1e3a5f;
            padding-top: 10px;
        }
        .conditions {
            float: left;
            width: 340px;
            font-size: 10px;
            color: #475569;
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            padding: 10px;
            border-radius: 4px;
        }
        .clear {
            clear: both;
        }
        .footer {
            margin-top: 40px;
            border-top: 1px solid #cbd5e1;
            padding-top: 12px;
            text-align: center;
            font-size: 9px;
            color: #64748b;
            line-height: 1.4;
        }
        .statut-badge {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 3px;
            font-size: 10px;
            font-weight: bold;
            text-transform: uppercase;
        }
        .statut-payee { background-color: #d1fae5; color: #065f46; }
        .statut-emise { background-color: #e0f2fe; color: #0369a1; }
        .statut-brouillon { background-color: #fef3c7; color: #92400e; }
        .statut-annulee { background-color: #ffe4e6; color: #9f1239; }
    </style>
</head>
<body>

    <!-- En-tête -->
    <div class="header">
        <table>
            <tr>
                <td style="width: 55%; vertical-align: top;">
                    <div class="company-name">{{ strtoupper($param->nom_entreprise ?: 'KHADIJA CONSULTING') }}</div>
                    <div class="company-desc">Cabinet de Conseil en Stratégie, Organisation & Management RH</div>
                    <div class="company-meta">
                        {{ $param->adresse ?: 'Dakar, Sénégal' }}<br>
                        @if($param->telephone)Tél : {{ $param->telephone }}<br>@endif
                        @if($param->email)Email : {{ $param->email }}<br>@endif
                        @if($param->nine_a)NINEA : {{ $param->nine_a }} @if($param->rc_cm)| RC : {{ $param->rc_cm }}@endif @endif
                    </div>
                </td>
                <td style="width: 45%; vertical-align: top;" class="facture-box">
                    <div class="facture-title">FACTURE</div>
                    <div class="facture-num">{{ $facture->numero }}</div>
                    <div class="facture-dates">
                        <strong>Date :</strong> {{ $facture->date_facture?->format('d/m/Y') }}<br>
                        @if($facture->date_echeance)<strong>Échéance :</strong> {{ $facture->date_echeance->format('d/m/Y') }}<br>@endif
                        <span class="statut-badge statut-{{ $facture->statut }}">
                            {{ strtoupper($facture->libelleStatut()) }}
                        </span>
                    </div>
                </td>
            </tr>
        </table>
    </div>

    <!-- Client -->
    <div class="client-box">
        <table>
            <tr>
                <td style="width: 50%; vertical-align: top;">
                    <div class="client-title">Facturé à :</div>
                    <div class="client-name">{{ $facture->client?->nom }}</div>
                    <div class="client-details">
                        @if($facture->client?->contact_nom)Attn : {{ $facture->client->contact_nom }}<br>@endif
                        @if($facture->client?->adresse){{ $facture->client->adresse }}<br>@endif
                        {{ $facture->client?->ville ?: 'Dakar, Sénégal' }}
                    </div>
                </td>
                <td style="width: 50%; vertical-align: top;">
                    <div class="client-details" style="padding-top: 15px;">
                        @if($facture->client?->telephone)<strong>Tél :</strong> {{ $facture->client->telephone }}<br>@endif
                        @if($facture->client?->email)<strong>Email :</strong> {{ $facture->client->email }}<br>@endif
                        @if($facture->client?->ninea)<strong>NINEA :</strong> {{ $facture->client->ninea }}<br>@endif
                        @if($facture->client?->rc_cm)<strong>RC :</strong> {{ $facture->client->rc_cm }}<br>@endif
                    </div>
                </td>
            </tr>
        </table>
    </div>

    <!-- Lignes de facture -->
    <table class="lignes-table">
        <thead>
            <tr>
                <th style="width: 50%;">Désignation de la prestation</th>
                <th style="width: 12%;" class="text-center">Quantité</th>
                <th style="width: 18%;" class="text-right">Prix Unitaire HT</th>
                <th style="width: 20%;" class="text-right">Montant HT</th>
            </tr>
        </thead>
        <tbody>
            @foreach($facture->lignes as $ligne)
                <tr>
                    <td><strong>{{ $ligne->designation }}</strong></td>
                    <td class="text-center">{{ number_format($ligne->quantite, 1, ',', '') }}</td>
                    <td class="text-right">{{ number_format($ligne->prix_unitaire, 0, ',', ' ') }} FCFA</td>
                    <td class="text-right">{{ number_format($ligne->total_ligne, 0, ',', ' ') }} FCFA</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <!-- Totaux & Règlement -->
    <div>
        <div class="conditions">
            <strong>Modalités & Règlement :</strong><br>
            Mode : {{ $facture->libelleModePaiement() }}<br>
            @if($facture->conditions_reglement)
                {{ $facture->conditions_reglement }}<br>
            @endif
            @if($facture->statut === 'payee' && $facture->date_paiement)
                <span style="color: #065f46; font-weight: bold;">
                    Règlement enregistré le {{ $facture->date_paiement->format('d/m/Y') }}
                    @if($facture->reference_paiement) (Réf : {{ $facture->reference_paiement }}) @endif
                </span>
            @endif
        </div>

        <table class="totals-table">
            <tr>
                <td class="total-label">Total Hors Taxes (HT) :</td>
                <td class="total-value">{{ number_format($facture->total_ht, 0, ',', ' ') }} FCFA</td>
            </tr>
            @if($facture->appliquer_tva)
                <tr>
                    <td class="total-label">TVA ({{ number_format($facture->taux_tva, 0) }}%) :</td>
                    <td class="total-value">{{ number_format($facture->montant_tva, 0, ',', ' ') }} FCFA</td>
                </tr>
            @else
                <tr>
                    <td class="total-label">TVA :</td>
                    <td class="total-value" style="font-weight: normal; color: #64748b;">Exonéré</td>
                </tr>
            @endif
            <tr class="grand-total">
                <td class="total-label">Net à payer (TTC) :</td>
                <td class="total-value">{{ number_format($facture->total_ttc, 0, ',', ' ') }} FCFA</td>
            </tr>
        </table>
        <div class="clear"></div>
    </div>

    <div style="margin-top: 30px; font-size: 11px; color: #334155;">
        Arrêtée la présente facture à la somme de :<br>
        <strong style="color: #1e3a5f; text-transform: uppercase;">
            {{ number_format($facture->total_ttc, 0, ',', ' ') }} FRANCS CFA
        </strong>
    </div>

    <!-- Pied de page -->
    <div class="footer">
        {{ $param->nom_entreprise ?: 'KHADIJA CONSULTING' }} — {{ $param->adresse ?: 'Dakar, Sénégal' }}
        @if($param->nine_a) — NINEA : {{ $param->nine_a }} @endif
        @if($param->rc_cm) — RCCM : {{ $param->rc_cm }} @endif<br>
        Document généré électroniquement par le SIRH Khadija Consulting le {{ now()->format('d/m/Y à H:i') }}
    </div>

</body>
</html>
