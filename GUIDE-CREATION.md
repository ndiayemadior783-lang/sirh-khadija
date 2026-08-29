# GUIDE COMPLET DE CRÉATION DU SIRH — KHADIJA CONSULTING

Ce document explique de A à Z comment créer, de zéro, le Système d'Information des
Ressources Humaines (SIRH) développé en Laravel pour le cabinet Khadija Consulting.
Il est destiné à la soutenance de licence IG : il justifie chaque étape du
développement et du déploiement.

============================================================
PARTIE 1 — PRÉREQUIS
============================================================

1.1. Outils nécessaires
- PHP 8.3 (ou supérieur)     ->  php -v
- Composer                   ->  composer -V
- Node.js 20+ (pour les assets front)  ->  node -v
- Laragon (Apache + MySQL en local) — ou un serveur WAMP/XAMPP
- VS Code + extensions : PHP Intelephense, Laravel Extension Pack,
  Blade Snippets, Tailwind CSS IntelliSense
- Git (optionnel)

1.2. Vérifier l'environnement
    php -v
    composer -V
    node -v
    npm -v

============================================================
PARTIE 2 — CRÉATION DU PROJET LARAVEL
============================================================

2.1. Installer l'installeur Laravel (une seule fois)
    composer global require laravel/installer

2.2. Créer le projet
    composer create-project laravel/laravel sirh-khadija

    -> à la fin, Laravel crée la structure de base et un fichier .env
    -> il génère aussi la clé d'application (APP_KEY) automatiquement

2.3. Installer l'authentification avec Breeze (login / register / logout)
    composer require laravel/breeze --dev
    php artisan breeze:install blade

    Structure générée :
    - app/Http/Controllers/Auth/...  (login, register, password)
    - resources/views/auth/...       (vues d'authentification)
    - routes/auth.php                (routes d'authentification)

IMPORTANT (Laravel 12) : il faut charger les routes d'authentification
dans bootstrap/app.php, sinon la page /login renvoie une erreur 404.

    bootstrap/app.php :

    return Application::configure(basePath: dirname(__DIR__))
        ->withRouting(
            web: __DIR__.'/../routes/web.php',
            commands: __DIR__.'/../routes/console.php',
            health: '/up',
            then: function () {
                Route::middleware('web')
                    ->group(base_path('routes/auth.php'));
            },
        )
        ...

2.4. Configurer la base de données
    Par défaut Laravel utilise SQLite (simple pour la démo).
    - Le fichier database/database.sqlite doit exister.
    - Dans .env :
        DB_CONNECTION=sqlite

    Pour MySQL en production (Laragon) :
        DB_CONNECTION=mysql
        DB_HOST=127.0.0.1
        DB_PORT=3306
        DB_DATABASE=sirh_khadija
        DB_USERNAME=root
        DB_PASSWORD=

============================================================
PARTIE 3 — BASE DE DONNÉES (MIGRATIONS)
============================================================

On crée les tables via des migrations. Chaque migration est un fichier PHP
dans database/migrations qui décrit le schéma.

3.1. Ajouter le rôle et des colonnes à la table users
    Créer : database/migrations/xxxx_add_role_to_users_table.php
    - role : admin | rh | manager | employe
    - phone, is_active

3.2. Table departements (départements du cabinet)
    - id, nom, description, responsable

3.3. Table postes (postes rattachés à un département)
    - id, departement_id (FK), libelle, description

3.4. Table employees (les employés)
    - user_id (FK, optionnel), matricule (unique), prenom, nom, genre,
      date_naissance, email, telephone, adresse, ville, nationalite, cin,
      date_embauche, statut, poste_id, departement_id, responsable_id,
      salaire_base, photo

3.5. Table contrats (contrats de travail)
    - employee_id (FK), type (CDI/CDD/stage/freelance), date_debut, date_fin,
      salaire_base, statut

3.6. Table presences (pointage)
    - employee_id (FK), date (unique par employé+date), heure_arrivee,
      heure_depart, statut, heures_sup, notes

3.7. Table conges (demandes de congés)
    - employee_id (FK), type, date_debut, date_fin, jours, motif, statut,
      valide_par (FK users), date_validation

3.8. Table formations + table pivot formation_employee
    - formations : titre, description, date_debut, date_fin, formateur, lieu, cout
    - formation_employee : formation_id, employee_id, statut (relation many-to-many)

3.9. Table primes
    - employee_id (FK), libelle, montant, date

3.10. Table bulletins_paie
    - numero (unique), employee_id (FK), period (ex: 2026-08), salaire_base,
      primes, heures_sup, indemnites, salaire_brut, cotisations_sociales,
      impots, salaire_net, statut, date_paiement, notes

Exécuter toutes les migrations :
    php artisan migrate

Vider et recréer la base :
    php artisan migrate:fresh

============================================================
PARTIE 4 — MODÈLES ELOQUENT
============================================================

Chaque table a un modèle. Exemple du modèle Employe :

    <?php
    namespace App\Models;
    use Illuminate\Database\Eloquent\Model;

    class Employe extends Model
    {
        protected $table = 'employees';
        protected $fillable = ['user_id','matricule','prenom','nom', ...];
        protected $casts = ['date_embauche' => 'date', ...];

        public function poste()  { return $this->belongsTo(Poste::class); }
        public function departement() { return $this->belongsTo(Departement::class); }
        public function conges() { return $this->hasMany(Conge::class,'employee_id'); }
        public function presences() { return $this->hasMany(Presence::class,'employee_id'); }
        public function formations() {
            return $this->belongsToMany(Formation::class,'formation_employee',
                'employee_id','formation_id')->withPivot('statut');
        }
    }

Point clé : comme le modèle s'appelle Employe mais que la table s'appelle
employees, on force le nom avec 'protected $table'. De même, les relations
one-to-many précisent la clé étrangère (employee_id).

============================================================
PARTIE 5 — RÔLES ET SÉCURITÉ
============================================================

5.1. Méthodes d'aide dans le modèle User
    - estAdmin()  : role === admin
    - estRH()     : admin OU rh
    - estGestion(): admin OU rh OU manager
    - libelleRole(): nom lisible du rôle

5.2. Middleware de contrôle des rôles
    Créer : app/Http/Middleware/VerifierRole.php

    public function handle($request, $next, ...$roles) {
        $user = $request->user();
        if (!$user) return redirect()->route('login');
        if (in_array($user->role, $roles, true)) return $next($request);
        return redirect()->route('dashboard')->with('error','Accès non autorisé');
    }

5.3. Enregistrer l'alias dans bootstrap/app.php
    $middleware->alias(['role' => VerifierRole::class]);

5.4. Protéger les routes dans routes/web.php
    Route::get('employes/create', ...)->middleware('role:admin,rh')->name(...)
    Route::get('bulletins', ...)->middleware('role:admin,rh')->name(...)

============================================================
PARTIE 6 — LES CONTRÔLEURS (LOGIC MÉTIER)
============================================================

Chaque module possède un contrôleur dans app/Http/Controllers.

6.1. DashboardController
    - index(): agrège les statistiques (effectif, masse salariale, congés
      en attente, répartition par département, évolution de la paie sur 6 mois)
    - Si l'utilisateur est un employé -> vue "espace personnel" (dashboard-employe)

6.2. EmployeController (CRUD complet)
    - index(): liste + filtres (recherche, département, statut)
    - create()/store(): création avec matricule automatique KC-XXX
      + création optionnelle d'un compte de connexion
    - show(): fiche détaillée (poste, contrat, dernier bulletin, ancienneté)
    - edit()/update(): modification
    - destroy(): suppression

6.3. ContratController / DepartementController
    - CRUD simple pour les contrats et les départements

6.4. PresenceController
    - index(): feuille de présence par date + statistiques
    - store(): pointage via updateOrCreate (une présence par employé/jour)

6.5. CongeController
    - index(): filtre par statut (et par employé si l'utilisateur est employé)
    - store(): calcul automatique du nombre de jours
    - valider()/rejeter(): circuit de validation avec trace de l'auteur
    - Pour un employé, employee_id est défini automatiquement à partir du compte

6.6. FormationController
    - CRUD des formations + inscription des employés (attach)

6.7. PrimeController
    - CRUD des primes / indemnités

6.8. BulletinPaieController
    - generer(): calcule et crée les bulletins pour une période
      RÈGLES : heures sup = heures × 1500 F
              brut = base + primes + heures sup + indemnités
              cotisations = 6% du brut
              impôt = 8% du (brut - cotisations)
              net = brut - cotisations - impôt
    - show(): affiche le bulletin détaillé et imprimable

============================================================
PARTIE 7 — LES ROUTES
============================================================

Le fichier routes/web.php regroupe toutes les routes de l'application,
protégées par le middleware auth, et avec les restrictions de rôle selon
le module. Voir la section 5.4.

============================================================
PARTIE 8 — LES VUES BLADE (FRONTEND)
============================================================

Le front utilise Blade + Tailwind CSS + Alpine.js + Chart.js.

8.1. Layout principal : resources/views/layouts/app.blade.php
    - Barre latérale (sidebar) fixe à gauche
    - Zone du contenu principal (main)
    - Affichage des messages de session (success / error)

8.2. Navigation : resources/views/layouts/navigation.blade.php
    - Liens conditionnels selon le rôle (estGestion / estRH)
    - Avatar + nom + déconnexion

8.3. Chaque module a son dossier de vues :
    resources/views/employes/  (index, create, edit, show)
    resources/views/contrats/  (index, create, show)
    resources/views/departements/ (index, create)
    resources/views/presences/ (index, create)
    resources/views/conges/    (index, create)
    resources/views/formations/(index, create, show)
    resources/views/primes/    (index, create)
    resources/views/bulletins/ (index, show)

8.4. Styles personnalisés : resources/css/app.css
    - Classes utilitaires : .stat-card, .btn-primary, .sidebar-link,
      .badge, .card ...

8.5. Compiler les assets
    npm install
    npm run build        (compile et publie dans public/build)

============================================================
PARTIE 9 — DONNÉES DE DÉMONSTRATION (SEEDERS)
============================================================

Le seeder crée :
- 3 comptes : admin, rh, manager (mot de passe : password)
- 7 départements, 17 postes, 18 employés, contrats, présences,
  congés, formations, primes, bulletins de paie (3 mois)

    php artisan db:seed

Pour repartir de zéro (vider les données de démo) :
    php artisan db:seed --class=CleanDatabaseSeeder
    (ou double-cliquer sur vider-data.bat)

============================================================
PARTIE 10 — LANCEMENT ET DÉPLOIEMENT
============================================================

10.1. En développement
    php artisan serve
    -> http://127.0.0.1:8000

    Le fichier demarrer.bat automatise tout :
    - composer install
    - création du .env + APP_KEY
    - préparation de la base (migrate + seed)
    - compilation des assets
    - lancement du serveur

10.2. Sur Laragon (production locale)
    1. Copier le dossier dans C:\laragon\www\sirh-khadija
    2. Créer la base sirh_khadija dans phpMyAdmin
    3. Configurer .env en MySQL
    4. Lancer Apache + MySQL
    5. Accéder à http://sirh-khadija.test

10.3. Optimisations avant mise en production
    php artisan config:cache
    php artisan route:cache
    php artisan view:cache
    php artisan migrate --force

============================================================
PARTIE 11 — COMPTES DE DÉMONSTRATION
============================================================

Admin    : admin@khadijaconsulting.sn   / password
RH       : rh@khadijaconsulting.sn      / password
Manager  : manager@khadijaconsulting.sn / password
Employé  : celui créé avec la case "créer un compte" (password par défaut)

============================================================
RÉSUMÉ DES COMMANDES PRINCIPALES
============================================================

# Installer
composer create-project laravel/laravel sirh-khadija
composer require laravel/breeze --dev
php artisan breeze:install blade
npm install

# Base de données
php artisan migrate            # créer les tables
php artisan db:seed            # données de démo
php artisan migrate:fresh --seed  # tout reconstruire + recharger

# Front
npm run build                  # compiler Tailwind/Vite

# Lancer
php artisan serve              # localhost:8000