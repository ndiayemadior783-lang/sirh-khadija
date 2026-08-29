<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Contrat extends Model
{
    use HasFactory;

    protected $fillable = ['employee_id', 'type', 'date_debut', 'date_fin', 'salaire_base', 'statut'];

    protected $casts = [
        'date_debut' => 'date',
        'date_fin' => 'date',
        'salaire_base' => 'decimal:2',
    ];

    public function employee()
    {
        return $this->belongsTo(Employe::class, 'employee_id');
    }

    public function libelleType(): string
    {
        return match ($this->type) {
            'CDI' => 'CDI',
            'CDD' => 'CDD',
            'stage' => 'Stage',
            'freelance' => 'Freelance',
            default => $this->type,
        };
    }
}