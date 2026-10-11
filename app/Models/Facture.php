<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Facture extends Model
{
    use HasFactory;

    protected $fillable = [
        'numero',
        'client_id',
        'date_facture',
        'date_echeance',
        'statut',
        'mode_paiement',
        'date_paiement',
        'reference_paiement',
        'appliquer_tva',
        'taux_tva',
        'total_ht',
        'montant_tva',
        'total_ttc',
        'conditions_reglement',
        'notes',
        'created_by',
    ];

    protected $casts = [
        'date_facture' => 'date',
        'date_echeance' => 'date',
        'date_paiement' => 'date',
        'appliquer_tva' => 'boolean',
        'taux_tva' => 'decimal:2',
        'total_ht' => 'decimal:2',
        'montant_tva' => 'decimal:2',
        'total_ttc' => 'decimal:2',
    ];

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function lignes()
    {
        return $this->hasMany(FactureLigne::class);
    }

    public function createur()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function libelleStatut(): string
    {
        return match ($this->statut) {
            'brouillon' => 'Brouillon',
            'emise' => 'Émise',
            'payee' => 'Payée',
            'annulee' => 'Annulée',
            default => ucfirst($this->statut),
        };
    }

    public function badgeClasses(): string
    {
        return match ($this->statut) {
            'payee' => 'bg-emerald-100 text-emerald-800 border-emerald-200',
            'emise' => 'bg-sky-100 text-sky-800 border-sky-200',
            'brouillon' => 'bg-amber-100 text-amber-800 border-amber-200',
            'annulee' => 'bg-rose-100 text-rose-800 border-rose-200',
            default => 'bg-slate-100 text-slate-800 border-slate-200',
        };
    }

    public function libelleModePaiement(): string
    {
        return match ($this->mode_paiement) {
            'virement' => 'Virement bancaire',
            'cheque' => 'Chèque',
            'especes' => 'Espèces',
            'wave' => 'Wave',
            'orange_money' => 'Orange Money',
            default => $this->mode_paiement ? ucfirst($this->mode_paiement) : 'Non spécifié',
        };
    }

    public static function genererNumero(): string
    {
        $annee = now()->format('Y');
        $mois = now()->format('m');
        $prefix = "FAC-{$annee}{$mois}-";

        $dernier = static::where('numero', 'like', "{$prefix}%")
            ->orderByDesc('id')
            ->value('numero');

        if ($dernier) {
            $num = (int) substr($dernier, -4) + 1;
        } else {
            $num = 1;
        }

        return $prefix . str_pad((string) $num, 4, '0', STR_PAD_LEFT);
    }
}
