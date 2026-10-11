@echo off
title SIRH Khadija Consulting
cd /d "%~dp0"

echo ============================================
echo    SIRH - KHADIJA CONSULTING
echo    Systeme d'Information des Ressources Humaines
echo ============================================
echo.

if not exist vendor\autoload.php (
    echo [1/4] Installation des dependances Composer...
    call composer install --no-interaction
) else (
    echo [1/4] Dependances deja installees.
)

if not exist .env (
    echo [2/4] Creation du fichier .env...
    copy .env.example .env > nul
    call php artisan key:generate
) else (
    echo [2/4] Fichier .env deja present.
)

if not exist database\database.sqlite (
    echo [3/4] Preparation de la base de donnees...
    type nul > database\database.sqlite
    call php artisan migrate --force
    call php artisan db:seed --force
) else (
    echo [3/4] Base de donnees deja prete.
)

if not exist public\build\manifest.json (
    echo [4/4] Compilation des assets (premiere fois)...
    call npm install --ignore-scripts > nul 2>&1
    call npm run build > nul 2>&1
) else (
    echo [4/4] Assets deja compiles.
)

echo.
echo ============================================
echo    Demarrage du serveur : http://127.0.0.1:8000
echo    Ctrl+C pour arreter
echo    (le worker e-mails tourne dans une fenetre reduite)
echo ============================================
echo.
start "SIRH - File d'attente e-mails" /min cmd /k "php artisan queue:work --sleep=3 --tries=3"
call php artisan serve