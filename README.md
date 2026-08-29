# SIRH Khadija Consulting

**Système d'Information des Ressources Humaines** développé dans le cadre du projet de licence IG sur « la digitalisation des processus RH : cas de Khadija Consulting ».

## 🎯 Présentation

Le SIRH vise à automatiser et numériser l'ensemble des processus de gestion des ressources humaines du cabinet de conseil Khadija Consulting :

- Gestion administrative des employés (fiches, postes, départements)
- Suivi des contrats de travail (CDI, CDD, stage, freelance)
- Pointage et suivi des présences
- Gestion des congés et absences avec circuit de validation
- Plan de formation et inscription du personnel
- Primes, indemnités et éléments variables de rémunération
- Génération des bulletins de paie avec calcul automatique
- Tableau de bord avec indicateurs et graphiques
- Gestion des rôles et des accès (admin, RH, manager, employé)

## 🛠️ Technologies

| Couche | Technologie |
|--------|-------------|
| Backend | PHP 8.3 · Laravel 12 |
| Frontend | Blade · Tailwind CSS · Alpine.js · Chart.js |
| Base de données | SQLite (démo) / MySQL (production) |
| Outils | Laragon, Composer, npm/Vite |

## 📀 Installation (déploiement local)

### Prérequis
- PHP ≥ 8.3
- Composer
- Node.js ≥ 20
- Laragon (optionnel, pour un serveur Apache/MySQL intégré)

### Étapes

```bash
# 1. Installer les dépendances
composer install

# 2. Configurer l'environnement
copy .env.example .env
php artisan key:generate

# 3. Configurer la base de données dans .env
#    (exemple MySQL via Laragon)
#    DB_CONNECTION=mysql
#    DB_HOST=127.0.0.1
#    DB_PORT=3306
#    DB_DATABASE=sirh_khadija
#    DB_USERNAME=root
#    DB_PASSWORD=

# 4. Créer la base et migrer
php artisan migrate

# 5. Charger les données de démonstration
php artisan db:seed

# 6. Compiler les assets
npm install
npm run build

# 7. Lancer le serveur
php artisan serve
# Accès : http://127.0.0.1:8000
```

## 🔑 Comptes de démonstration

| Rôle | Email | Mot de passe |
|------|-------|--------------|
| Administrateur | `admin@khadijaconsulting.sn` | `password` |
| Gestionnaire RH | `rh@khadijaconsulting.sn` | `password` |
| Manager | `manager@khadijaconsulting.sn` | `password` |
| Employé | *voir fiche employé* | `password` |

## 🗂️ Modules fonctionnels

### 1. Tableau de bord
- Effectif, ancienneté, départements
- Masse salariale et évolution sur 6 mois (graphique)
- Répartition par département (graphique en anneau)
- Derniers employés et demandes de congés

### 2. Gestion des employés
- Création, modification, consultation des fiches
- Matricule automatique (KC-XXX)
- Rattachement poste / département / responsable
- Salaire de base, statut, coordonnées
- Création optionnelle d'un compte de connexion

### 3. Contrats de travail
- CRUD des contrats (CDI, CDD, stage, freelance)
- Suivi des dates et du statut

### 4. Départements
- Organigramme par services
- Nombre d'employés et de postes par département

### 5. Présences & pointage
- Feuille de présence par jour
- Saisie arrivée / départ / heures supplémentaires
- Statistiques de présence et taux du jour

### 6. Congés & absences
- Démarrage d'une demande (employé ou RH)
- Types : payé, maladie, maternité, sans solde, exceptionnel
- Circuit de validation (valider / refuser) par les gestionnaires

### 7. Formations
- Catalogue des formations (dates, formateur, coût)
- Inscription des participants

### 8. Primes & indemnités
- Ajout de primes par employé
- Suivi des montants et dates

### 9. Bulletins de paie
- Génération automatique par période
- Calcul : brut = base + primes + heures sup + indemnités
- Retenues : cotisations sociales (6%) + impôt (8%)
- Net à payer
- Bulletin imprimable (format B1)

## 🧮 Règles de calcul de la paie

```
Heures supplémentaires = heures_sup × 1 500 FCFA
Salaire brut = salaire de base + primes + heures sup + indemnités
Cotisations sociales = 6 % du brut
Impôt sur le revenu = 8 % du (brut − cotisations)
Salaire net = brut − cotisations − impôt
```

## 👥 Gestion des rôles et accès

| Module | Admin | RH | Manager | Employé |
|--------|:-----:|:--:|:-------:|:-------:|
| Tableau de bord | ✅ | ✅ | ✅ | ✅ (espace perso) |
| Employés (lecture) | ✅ | ✅ | ✅ | ❌ |
| Employés (écriture) | ✅ | ✅ | ❌ | ❌ |
| Contrats | ✅ | ✅ | ❌ | ❌ |
| Départements | ✅ | ✅ | ❌ | ❌ |
| Présences (lecture) | ✅ | ✅ | ✅ | ❌ |
| Présences (écriture) | ✅ | ✅ | ❌ | ❌ |
| Congés | ✅ | ✅ | ✅ | ✅ (le sien) |
| Validation congés | ✅ | ✅ | ✅ | ❌ |
| Formations (lecture) | ✅ | ✅ | ✅ | ❌ |
| Formations (écriture) | ✅ | ✅ | ❌ | ❌ |
| Primes | ✅ | ✅ | ❌ | ❌ |
| Bulletins de paie | ✅ | ✅ | ❌ | ❌ |

## 📦 Structure du code

```
app/
├── Http/Controllers/   # Logique métier des modules
│   └── Auth/           # Authentification (Breeze)
├── Http/Middleware/    # VerifierRole (contrôle des rôles)
└── Models/             # Modèles Eloquent + relations
database/
├── migrations/         # Schéma de la base (11 tables)
└── seeders/            # Données de démonstration Khadija
resources/views/        # Vues Blade (Tailwind)
routes/web.php          # Définition des routes
```

## 📄 Base de données (11 tables)

`users`, `departements`, `postes`, `employees`, `contrats`, `presences`, `conges`, `formations`, `formation_employee`, `primes`, `bulletins_paie`.

## 📦 Livrables et documentation

| Document | Description |
|----------|-------------|
| `OFFRE-KHADIJA-CONSULTING.md` | Offre commerciale client (versions DOCX et PDF dans le dossier Téléchargements) |
| `DEPLOIEMENT-PRODUCTION.md` | Guide de déploiement en production avec MySQL (cPanel / VPS) |
| `GUIDE-CREATION.md` | Guide complet de création du projet |
| `DEMO-SOUTENANCE.md` | Script de démonstration pour la soutenance |
| `HEBERGEMENT.md` | Guide d'hébergement (local, Render démo, cPanel, VPS) |
| `sauvegarde-mysql.bat` et `sauvegarde-mysql.sh` | Sauvegarde automatique de la base MySQL |
| `restaurer-mysql.bat` | Restauration d'une sauvegarde MySQL |
| `test_flux_metier.php` | Script de validation des flux métier (employé, contrat, présence, congé, formation, prime, bulletin) |

## 🚀 Déploiement en production (exemple : Laragon / VPS)

### Sur Laragon
1. Copier le dossier projet dans `C:\laragon\www\sirh-khadija`
2. Créer la base `sirh_khadija` dans phpMyAdmin (MySQL)
3. Renseigner les identifiants dans `.env`
4. Lancer Apache + MySQL depuis le panneau Laragon
5. Accéder à `http://sirh-khadija.test`

### Conseils pour tout déploiement
```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan migrate --force
```

---

*Projet académique — Licence Informatique de Gestion*
_Khadija Consulting · Dakar, Sénégal_