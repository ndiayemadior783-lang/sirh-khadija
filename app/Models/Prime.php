<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Prime extends Model
{
    use HasFactory;

    protected $fillable = ['employee_id', 'libelle', 'montant', 'date'];

    protected $casts = [
        'date' => 'date',
    ];

    public function employee()
    {
        return $this->belongsTo(Employe::class, 'employee_id');
    }
}