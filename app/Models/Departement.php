<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Departement extends Model
{
    use HasFactory;

    protected $fillable = ['nom', 'description', 'responsable'];

    public function postes()
    {
        return $this->hasMany(Poste::class);
    }

    public function employes()
    {
        return $this->hasMany(Employe::class);
    }
}
