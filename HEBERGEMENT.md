# GUIDE D'HEBERGEMENT DU SIRH - KHADIJA CONSULTING

Ce guide explique comment mettre le SIRH en ligne selon 4 scénarios, du plus simple au plus professionnel :
1. Hebergement local / LAN (soutenance)
2. Hebergement gratuit en ligne (Render) - demo publique
3. Hebergement mutualise avec cPanel (Hostinger, OVH...) - MySQL
4. VPS Ubuntu + Nginx + MySQL (production complete)

====================================================================
OPTION 1 - HEBERGEMENT LOCAL / LAN (pour la soutenance)
====================================================================

Sur la machine de la soutenance :
    php artisan serve
    -> http://127.0.0.1:8000

Pour rendre l'application accessible depuis le reseau local (si autorise) :
    php artisan serve --host=0.0.0.0 --port=8000
    -> http://VOTRE_IP:8000  (ex : http://192.168.1.20:8000)

Avec Laragon, c'est encore plus simple :
    - demarrer Apache + MySQL dans le panneau Laragon
    - le projet dans C:\laragon\www\sirh-khadija est accede via http://sirh-khadija.test
    - Ne pas oublier d'activer le HTTPS avec le bouton "Switch to HTTPS" si besoin.

====================================================================
OPTION 2 - HEBERGEMENT GRATUIT EN LIGNE (RENDER) - DEMO PUBLIQUE
====================================================================

Render propose un plan gratuit pour les applications web. Il peut heberger
ce projet Laravel avec une base SQLite (suffisant pour une demo).

Etapes :
1. Creer un depot GitHub et pousser le projet :
       cd sirh-khadija
       git init
       git add .
       git commit -m "SIRH Khadija Consulting"
       git remote add origin https://github.com/VOTRE_COMPTE/sirh-khadija.git
       git push -u origin main

2. Creer un compte sur https://render.com (avec GitHub).

3. Cliquer "New > Web Service", connecter le depot GitHub.

4. Render detecte le fichier render.yaml du projet (deja prepare).
   Verifier :
   - Nom : sirh-khadija-rh
   - Runtime : PHP
   - Build Command : composer install --no-dev --optimize-autoloader
                     && php artisan key:generate
                     && npm install && npm run build
   - Start Command : php artisan serve --host=0.0.0.0 --port=$PORT
   - Disk (persistant) : monte /app/database (pour la base SQLite)
   - Variables : APP_ENV=production, APP_DEBUG=false, DB_CONNECTION=sqlite

5. Cliquer "Deploy". Render va cloner, installer, compiler et demarrer.
   Apres le premier deploiement :
   - Ouvrir la console du service (Shell) et lancer :
         php artisan migrate --force
         php artisan db:seed --force
   - L'application est alors en ligne sur https://sirh-khadija-rh.onrender.com

Remarques :
- Le plan gratuit se met en veille apres 15 min d'inactivite (rechargement lent).
- La base SQLite est persistee sur le disque monte (survie aux redeploiements).
- Pour securiser, activer l'authentification (Basic Auth) dans le menu Render.

====================================================================
OPTION 3 - HEBERGEMENT MUTUALISE (Hostinger, OVH, ZELEM...)
====================================================================

C'est la solution courante au Senegal / Afrique de l'Ouest (Hostinger,
OVH, ZeleM...). Elle inclut PHP 8+ et MySQL sans maintenance serveur.

A. Acheter / activer l'hebergement
   1. Choisir une offre "Hebergement web" compatible PHP 8.x + MySQL.
   2. Creer un domaine ou sous-domaine : sirh.khadijaconsulting.sn
   3. Dans cPanel, creer une base de donnees MySQL :
      - "MySQL Databases" : creer la base sirh_khadija
      - creer l'utilisateur et lui donner TOUS les droits sur la base

B. Preparer le projet en local
   1. Ouvrir le dossier sirh-khadija.
   2. Supprimer (ou ne PAS envoyer) : node_modules, vendor, .env
   3. Compiler les assets (pour ne pas avoir besoin de node sur le serveur) :
          npm install
          npm run build
   4. Si l'hebergement n'a PAS de terminal/SSH :
      - NE PAS supprimer vendor : l'envoyer tel quel (plus lourd mais fonctionnel)
   5. Tester en local avec MySQL :
      - cree la base sirh_khadija en local, adapter .env, puis :
          php artisan migrate
          php artisan db:seed
      - la base locale servira de sauvegarde.

C. Envoyer les fichiers via FTP (FileZilla) ou le gestionnaire de fichiers
   1. Se connecter au FTP de l'hebergement.
   2. Uploader le contenu du dossier sirh-khadija dans le dossier racine
      du domaine (souvent public_html ou www).
      IMPORTANT : le dossier PUBLIC de Laravel doit etre le dossier racine.
      La solution fiable sur cPanel : placer les fichiers dans public_html/sirh
      et pointer le domaine vers public_html/sirh/public (voir ci-apres).

D. Pointe le domaine vers /public
   Cas 1 - Sous domaine a la racine :
      Sur cPanel, le domaine principal pointe deja vers public_html.
      Il faut donc deplacer/proteger le dossier public Laravel.
      - Option DAP (Domain alias / addon) : creer un addon domain
        dont la racine = public_html/sirh-khadija/public

   Cas 2 - Si impossible, creer dans public_html/sirh-khadija un fichier
      .htaccess qui redirige tout vers /public :
      (ce fichier est fourni en exemple : htaccess-redirect.txt dans le projet)
      RewriteEngine On
      RewriteRule ^(.*)$ public/$1 [L]

E. Configurer le fichier .env en production
   Copier .env.production.example vers .env et remplir :
        APP_ENV=production
        APP_DEBUG=false
        APP_URL=https://sirh.khadijaconsulting.sn
        DB_CONNECTION=mysql
        DB_HOST=localhost
        DB_PORT=3306
        DB_DATABASE=uXXXXXX_sirh_khadija   (le nom cree dans cPanel)
        DB_USERNAME=uXXXXXX_sirh           (l'utilisateur cree)
        DB_PASSWORD=VotreMotDePasse

   Puis generer la cle :
        Via terminal de cPanel (si dispo) :
            php artisan key:generate
        Sinon : mettre dans .env une cle generee en local :
            php artisan key:generate --show
            (coller la ligne APP_KEY=base64:... dans le .env du serveur)

F. Migrer la base
   - Si l'hebergement fournit un terminal (hpanel/Hostinger, SSH) :
        php artisan migrate --force
        php artisan db:seed --force
   - Sinon : creer un petit script de migration protege (a supprimer ensuite) :
        dans public/migrer.php :
            <?php
            if ($_GET['cle'] !== 'MON_TOKEN_SECRET') { http_response_code(403); exit; }
            chdir(__DIR__.'/..');
            echo shell_exec('php artisan migrate --force 2>&1');
            echo shell_exec('php artisan db:seed --force 2>&1');
        - ouvrir https://domaine/migrer.php?cle=MON_TOKEN_SECRET
        - puis SUPPRIMER ce fichier immediatement.

G. Droits d'ecriture (permissions)
   - donner l'ecriture (775) a : storage/ et bootstrap/cache/
   - si la base SQLite est utilisee : database/database.sqlite doit etre
     ecrivable par l'utilisateur du serveur.

====================================================================
OPTION 4 - VPS UBUNTU + NGINX + MYSQL (PRODUCTION)
====================================================================

Un script automatise est fourni dans le projet : deploy-vps.sh
Il installe PHP 8.3, Nginx, MySQL, Composer, Node, clone le depot GitHub,
configure MySQL, migre les donnees, configure Nginx et installe HTTPS.

Utilisation :
    # sur le VPS (Ubuntu 22.04/24.04 neuf)
    sudo bash deploy-vps.sh sirh.khadijaconsulting.sn https://github.com/VOTRE_COMPTE/sirh-khadija.git

Avant d'exécuter :
    - le domaine doit pointer vers l'IP du VPS (zone DNS)
    - les ports 80 et 443 doivent etre ouverts
    - remplacer CHANGE_ME_SECURE_PASS dans le script par un mot de passe fort

Apres : https://sirh.khadijaconsulting.sn fonctionne avec certificat SSL.

====================================================================
CHECKLIST DE SECURITE AVANT LA MISE EN PRODUCTION
====================================================================

[x] APP_ENV=production dans .env
[x] APP_DEBUG=false (sinon les erreurs exposent le code)
[x] APP_KEY generee (php artisan key:generate)
[x] HTTPS actif (certificat SSL)
[x] Comptes de demo : changer le mot de passe par defaut
[x] Permissions : storage et bootstrap/cache en 775
[x] Supprimer les fichiers temporaires (migrer.php, etc.)
[x] Changer les taux de paie si besoin (dans le controleur BulletinPaie)

====================================================================
CHANGER SQLITE VERS MYSQL (SI NECESSAIRE)
====================================================================

1. Dans .env :
        DB_CONNECTION=mysql
        DB_HOST=127.0.0.1
        DB_PORT=3306
        DB_DATABASE=sirh_khadija
        DB_USERNAME=root
        DB_PASSWORD=...

2. Creer la base et migrer :
        php artisan migrate
        php artisan db:seed

3. Les requetes SQL utilisees par les bulletins utilisent SQLite (strftime).
   Pour MySQL, remplacer strftime('%Y-%m', date) par DATE_FORMAT(date, '%Y-%m')
   dans :
        - app/Http/Controllers/BulletinPaieController.php
        - app/Http/Controllers/EmployeController.php (presenceCeMois)
        - database/seeders/DatabaseSeeder.php

====================================================================
DEPANNAGE RAPIDE
====================================================================

- Page blanche / 500 : verifier storage/logs/laravel.log
- Erreur de cle : php artisan key:generate puis vider le cache
- Permission denyed : chmod -R 775 storage bootstrap/cache
- Base introuvable : verifier DB_HOST (localhost ou 127.0.0.1) et les droits
- Assets non charges : npm run build puis uploader public/build
- SITE en maintenance : php artisan up