<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Formation extends Model
{
    use HasFactory;

    protected $fillable = ['titre', 'description', 'date_debut', 'date_fin', 'formateur', 'lieu', 'cout'];

    protected $casts = [
        'date_debut' => 'date',
        'date_fin' => 'date',
    ];

    public function employes()
    {
        return $this->belongsToMany(Employe::class, 'formation_employee', 'formation_id', 'employee_id')->withPivot('statut')->withTimestamps();
    }
}