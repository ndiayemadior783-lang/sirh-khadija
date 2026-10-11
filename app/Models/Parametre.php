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
        'mail_actif', 'mail_host', 'mail_port', 'mail_encryption', 'mail_username',
        'mail_password', 'mail_from_address', 'mail_from_name', 'mail_notif_conges',
    ];

    protected $hidden = ['mail_password'];

    protected $casts = [
        'mail_actif' => 'boolean',
        'mail_notif_conges' => 'boolean',
        'mail_port' => 'integer',
        'mail_password' => 'encrypted',
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