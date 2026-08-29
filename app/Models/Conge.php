<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Conge extends Model
{
    use HasFactory;

    protected $fillable = ['employee_id', 'type', 'date_debut', 'date_fin', 'jours', 'motif', 'statut', 'valide_par', 'date_validation'];

    protected $casts = [
        'date_debut' => 'date',
        'date_fin' => 'date',
        'date_validation' => 'datetime',
    ];

    public function employee()
    {
        return $this->belongsTo(Employe::class, 'employee_id');
    }

    public function valideur()
    {
        return $this->belongsTo(User::class, 'valide_par');
    }

    public function libelleType(): string
    {
        return match ($this->type) {
            'paye' => 'Congé payé',
            'maladie' => 'Congé maladie',
            'maternite' => 'Congé maternité',
            'sans_solde' => 'Congé sans solde',
            'exceptionnel' => 'Congé exceptionnel',
            default => $this->type,
        };
    }
}