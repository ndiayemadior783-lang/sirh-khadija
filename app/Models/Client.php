<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    use HasFactory;

    protected $fillable = [
        'nom',
        'contact_nom',
        'email',
        'telephone',
        'adresse',
        'ville',
        'ninea',
        'rc_cm',
        'notes',
    ];

    public function factures()
    {
        return $this->hasMany(Facture::class);
    }

    public function totalFacture(): float
    {
        return (float) $this->factures()->sum('total_ttc');
    }

    public function totalPaye(): float
    {
        return (float) $this->factures()->where('statut', 'payee')->sum('total_ttc');
    }
}
