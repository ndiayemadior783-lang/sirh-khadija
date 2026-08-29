# DEPLOIEMENT EN PRODUCTION MYSQL - SIRH KHADIJA CONSULTING

Ce guide explique comment mettre le SIRH en ligne avec une base de donnees
**MySQL** (la reference pour la production), selon deux solutions professionnelles
adaptees au marche senegalais : **Hébergement mutualise (cPanel/Hostinger/OVH)**
ou **VPS Ubuntu + Nginx + MySQL**.

> **Point important** : ni Render ni Supabase ne fournissent MySQL. Render ne
> fournit que PostgreSQL/Redis et n'execute pas PHP de maniere fiable avec MySQL.
> Supabase n'heberge que PostgreSQL et n'exécute pas d'application PHP.
> Pour un deploiement **professionnel et vendable**, on utilise donc un
> hebergement MySQL classique (cPanel mutualise ou VPS) decrit ci-dessous.

---

## TABLE DES MATIERES

1. Preparation du projet en local
2. Option A - Hebergement mutualise avec cPanel (Hostinger, OVH, ZeleM)
3. Option B - VPS Ubuntu + Nginx + MySQL (production complete)
4. Configuration de la base de donnees MySQL
5. Securisation de l'application
6. Sauvegardes automatiques de la base
7. Checklist finale avant la vente / mise en service

---

## 1. PREPARATION DU PROJET EN LOCAL

Sur votre machine (Laragon), les fichiers sont dans `C:\laragon\www\sirh-khadija`.

### 1.1 Compiler les assets (CSS/JS)
Les assets doivent etre compiles afin de ne pas avoir besoin de Node sur le serveur :
```bash
cd C:\laragon\www\sirh-khadija
npm install
npm run build
```

### 1.2 Tester en local avec MySQL
La base locale `sirh_khadija` (MySQL via Laragon) servira de reference et de
sauvegarde. Pour repartir d'une base vide :
```bash
php artisan migrate:fresh --seed
```

### 1.3 Dossier a envoyer sur le serveur
- Envoyer le contenu du projet **sauf** : `node_modules`, `vendor` (optionnel), `.env`.
- Si l'hebergement n'a pas de terminal/SSH, envoyer aussi `vendor/` (plus lourd mais
  fonctionnel sans lancer `composer install` sur le serveur).

---

## 2. OPTION A - HEBERGEMENT MUTUALISE AVEC cPANEL

C'est la solution courante et economique au Senegal / Afrique de l'Ouest
(Hostinger, OVH, ZeleM...). Elle inclut PHP 8.x et MySQL sans maintenance serveur.

### Etape A1 - Acheter l'hebergement et un domaine
- Choisir une offre « Hebergement web » compatible PHP 8.x + MySQL.
- Acheter un nom de domaine : par exemple `sirh.khadijaconsulting.sn`.

### Etape A2 - Creer la base de donnees MySQL dans cPanel
1. Ouvrir le panneau cPanel.
2. Section **MySQL Databases** :
   - Creer la base `sirh_khadija` (ou `uXXXXXX_sirh_khadija`).
   - Creer un utilisateur MySQL et lui donner **TOUS les privileges** sur cette base.
   - Noter : nom de la base, utilisateur, mot de passe.

### Etape A3 - Uploader les fichiers du projet
- Utiliser FileZilla (FTP) ou le gestionnaire de fichiers de cPanel.
- Deplacer le contenu du projet dans `public_html/sirh` (ou le dossier racine du domaine).
- **IMPORTANT** : le dossier `public` de Laravel doit etre le dossier racine servi par
  le web. Sur cPanel, on pointe le domaine vers `public_html/sirh/public`.

### Etape A4 - Pointer le domaine vers le dossier /public
- **Cas 1 - Sous-domaine ou domaine principal** :
  - Option « Addon Domain » ou « Subdomain » sur cPanel avec racine =
    `public_html/sirh/public`.
- **Cas 2 - Si impossible** :
  - Creer dans `public_html/sirh` un fichier `.htaccess` qui redirige tout vers `/public`.
  - Un modele est fourni dans le projet : `htaccess-redirect.txt`.
  ```apache
  RewriteEngine On
  RewriteRule ^(.*)$ public/$1 [L]
  ```

### Etape A5 - Configurer le fichier .env en production
Copier `.env.production.example` vers `.env` et remplir :
```ini
APP_ENV=production
APP_DEBUG=false
APP_URL=https://sirh.khadijaconsulting.sn

DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=sirh_khadija
DB_USERNAME=votre_utilisateur
DB_PASSWORD=VotreMotDePasse
```

### Etape A6 - Generer la cle d'application (APP_KEY)
- Via terminal cPanel (si disponible) :
  ```bash
  php artisan key:generate
  ```
- Sinon, generer en local et coller la ligne dans le `.env` du serveur :
  ```bash
  php artisan key:generate --show
  ```

### Etape A7 - Migrer et charger les donnees
- Si l'hebergement fournit un terminal/SSH :
  ```bash
  php artisan migrate --force
  php artisan db:seed --force
  ```
- Sinon : creer un petit script securise, a SUPPRIMER apres usage :
  - Creer `public/migrer.php` :
    ```php
    <?php
    if ($_GET['cle'] !== 'VOTRE_TOKEN_SECRET') { http_response_code(403); exit; }
    chdir(__DIR__.'/..');
    echo shell_exec('php artisan migrate --force 2>&1');
    echo shell_exec('php artisan db:seed --force 2>&1');
    ```
  - Ouvrir `https://sirh.khadijaconsulting.sn/migrer.php?cle=VOTRE_TOKEN_SECRET`
  - **Supprimer immediatement le fichier** apres la migration.

### Etape A8 - Droits d'ecriture
- Donner l'ecriture (775) a : `storage/` et `bootstrap/cache/`.

---

## 3. OPTION B - VPS UBUNTU + NGINX + MYSQL

C'est la solution la plus complete et independante. Un script automatise est fourni
dans le projet : `deploy-vps.sh`. Il installe PHP 8.3, Nginx, MySQL, Composer, Node,
clone le depot GitHub, configure MySQL, migre les donnees, configure Nginx et installe
le HTTPS (Let's Encrypt).

### Etape B1 - Preparer un VPS Ubuntu 22.04/24.04
- Acheter un VPS (Hebergeurs locaux : ZeleM, Wingu, ou internationaux : DigitalOcean,
  Hostinger VPS, OVH).
- Le domaine doit pointer vers l'IP du VPS (zone DNS).
- Ouvrir les ports 80 et 443.

### Etape B2 - Executer le script de deploiement
Connectez-vous au VPS en SSH, puis :
```bash
sudo bash deploy-vps.sh sirh.khadijaconsulting.sn https://github.com/VOTRE_COMPTE/sirh-khadija.git
```
Avant d'executer, remplacer dans `deploy-vps.sh` le mot de passe par defaut
`CHANGE_ME_SECURE_PASS` par un mot de passe fort pour MySQL.

### Etape B3 - Resultat
Apres l'execution, `https://sirh.khadijaconsulting.sn` fonctionne avec un certificat
SSL. La base MySQL `sirh_khadija` est creee et migree.

---

## 4. CONFIGURATION DE LA BASE DE DONNEES MYSQL

Le projet est **deja configure pour MySQL** en local et en production. Dans `.env` :

```ini
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=sirh_khadija
DB_USERNAME=root        # en local Laragon
DB_PASSWORD=            # vide en local
```

Pour repartir d'une base vide en local :
```bash
php artisan migrate:fresh --seed
```

Les anciennes requetes specifiques a SQLite (`strftime`) ont ete remplacees par leur
equivalent MySQL (`DATE_FORMAT`) dans tous les fichiers du projet. Aucune modification
n'est necessaire.

---

## 5. SECURISATION DE L'APPLICATION

- [ ] `APP_ENV=production` dans `.env`
- [ ] `APP_DEBUG=false` (sinon les erreurs exposent le code)
- [ ] `APP_KEY` generee (`php artisan key:generate`)
- [ ] HTTPS actif (certificat SSL)
- [ ] Changer les mots de passe des comptes par defaut
      (`admin@`, `rh@`, `manager@khadijaconsulting.sn`)
- [ ] Permissions : `storage` et `bootstrap/cache` en 775
- [ ] Supprimer les fichiers temporaires (`migrer.php`, etc.)
- [ ] Verifier les taux de paie dans le controleur `BulletinPaie`

---

## 6. SAUVEGARDES AUTOMATIQUES DE LA BASE

Un script de sauvegarde MySQL est fourni dans le projet :

### Sur Windows / Laragon
```bash
sauvegarde-mysql.bat
```
Cree un dump SQL dans le dossier `sauvegardes/` (a ne pas envoyer sur GitHub).

### Restauration
```bash
restaurer-mysql.bat chemin\vers\fichier.sql
```

### Sur Linux / VPS
Cree un dump dans `sauvegardes/` :
```bash
sudo bash sauvegarde-mysql.sh
```
Pour automatiser (cron quotidien), ajouter dans `crontab -e` :
```cron
0 2 * * * /chemin/vers/sauvegarde-mysql.sh >> /var/log/sirh-backup.log 2>&1
```

---

## 7. CHECKLIST FINALE AVANT LA VENTE / MISE EN SERVICE

- [ ] L'application fonctionne correctement en local avec MySQL
- [ ] Les tests des flux metier (ajout employe, formation, conge, presence, bulletin)
      ont ete valides
- [ ] Le site est en ligne avec HTTPS
- [ ] Les comptes par defaut ont ete personnalises
- [ ] Une sauvegarde de la base a ete effectuee et testee
- [ ] La formation du personnel RH est planifiee
- [ ] Le document d'offre (OFFRE-KHADIJA-CONSULTING.pdf) a ete presente au client

---

## DEPANNAGE RAPIDE

- **Page blanche / 500** : verifier `storage/logs/laravel.log`
- **Erreur de cle** : `php artisan key:generate` puis vider le cache
- **Permission denyed** : `chmod -R 775 storage bootstrap/cache`
- **Base introuvable** : verifier `DB_HOST` (localhost ou 127.0.0.1) et les droits
- **Assets non charges** : `npm run build` puis uploader `public/build`
- **Site en maintenance** : `php artisan up`