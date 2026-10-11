<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Employe extends Model
{
    use HasFactory;

    protected $table = 'employees';

    protected $fillable = [
        'user_id', 'matricule', 'prenom', 'nom', 'genre', 'date_naissance', 'lieu_naissance', 'email',
        'telephone', 'adresse', 'ville', 'nationalite', 'cin', 'date_embauche',
        'statut', 'poste_id', 'departement_id', 'responsable_id', 'salaire_base', 'photo',
    ];

    protected $casts = [
        'date_naissance' => 'date',
        'date_embauche' => 'date',
        'salaire_base' => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function poste()
    {
        return $this->belongsTo(Poste::class);
    }

    public function departement()
    {
        return $this->belongsTo(Departement::class);
    }

    public function responsable()
    {
        return $this->belongsTo(Employe::class, 'responsable_id');
    }

    public function subordonnes()
    {
        return $this->hasMany(Employe::class, 'responsable_id');
    }

    public function contrats()
    {
        return $this->hasMany(Contrat::class, 'employee_id');
    }

    public function presences()
    {
        return $this->hasMany(Presence::class, 'employee_id');
    }

    public function conges()
    {
        return $this->hasMany(Conge::class, 'employee_id');
    }

    public function primes()
    {
        return $this->hasMany(Prime::class, 'employee_id');
    }

    public function bulletins()
    {
        return $this->hasMany(BulletinPaie::class, 'employee_id');
    }

    public function formations()
    {
        return $this->belongsToMany(Formation::class, 'formation_employee', 'employee_id', 'formation_id')->withPivot('statut')->withTimestamps();
    }

    public function nomComplet(): string
    {
        return trim($this->prenom . ' ' . $this->nom);
    }

    public function getNomCompletAttribute(): string
    {
        return $this->nomComplet();
    }

    public function getAncienneteAttribute(): int
    {
        return $this->date_embauche ? now()->diffInYears($this->date_embauche) : 0;
    }

    public function getInitialesAttribute(): string
    {
        return strtoupper(mb_substr($this->prenom, 0, 1) . mb_substr($this->nom, 0, 1));
    }

    public function joursCongeAnnuel(): int
    {
        static $jours = null;
        if ($jours === null) {
            $jours = (int) (Parametre::instance()->jours_conge_paye ?: 22);
        }
        return $jours;
    }

    public function congesPris(?int $year = null, string $type = 'paye'): int
    {
        $year = $year ?? now()->year;

        return (int) $this->conges()
            ->where('type', $type)
            ->where('statut', 'valide')
            ->whereYear('date_debut', $year)
            ->sum('jours');
    }

    public function soldeConges(?int $year = null): int
    {
        return $this->joursCongeAnnuel() - $this->congesPris($year);
    }
}