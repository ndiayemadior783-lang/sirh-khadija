<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Poste extends Model
{
    use HasFactory;

    protected $fillable = ['departement_id', 'libelle', 'description'];

    public function departement()
    {
        return $this->belongsTo(Departement::class);
    }

    public function employes()
    {
        return $this->hasMany(Employe::class);
    }
}
