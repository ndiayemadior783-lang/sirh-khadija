<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Presence extends Model
{
    use HasFactory;

    protected $fillable = ['employee_id', 'date', 'heure_arrivee', 'heure_depart', 'statut', 'heures_sup', 'notes'];

    protected $casts = [
        'date' => 'date',
    ];

    public function employee()
    {
        return $this->belongsTo(Employe::class, 'employee_id');
    }
}