<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class BulletinPaie extends Model
{
    use HasFactory;

    protected $table = 'bulletins_paie';

    protected $fillable = [
        'numero', 'employee_id', 'period', 'salaire_base', 'primes', 'heures_sup',
        'indemnites', 'salaire_brut', 'cotisations_sociales', 'impots', 'salaire_net',
        'statut', 'date_paiement', 'notes',
    ];

    protected $casts = [
        'date_paiement' => 'date',
    ];

    public function employee()
    {
        return $this->belongsTo(Employe::class, 'employee_id');
    }
}