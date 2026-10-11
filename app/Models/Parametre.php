<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Parametre extends Model
{
    protected $fillable = [
        'nom_entreprise', 'adresse', 'telephone', 'email', 'nine_a', 'rc_cm',
        'ipres_employeur', 'ipres_employe', 'ipm_employeur', 'ipm_employe',
        'cfrp_employeur', 'cfrp_employe', 'taxe_exceptionnelle',
        'taux_cotisation', 'taux_impot', 'taux_horaire', 'jours_conge_paye',
    ];

    public function scopes()
    {
        return [];
    }

    public static function instance(): static
    {
        $param = static::first();
        if (!$param) {
            $param = static::create(['nom_entreprise' => 'Khadija Consulting']);
        }
        return $param;
    }
}