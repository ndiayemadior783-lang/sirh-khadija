#!/bin/sh
set -e

echo "[SIRH] Demarrage du conteneur..."

# Creer le dossier database et le fichier SQLite
mkdir -p database
touch database/database.sqlite

# Rendre le dossier database accessible en ecriture par Apache (www-data)
chown -R www-data:www-data database/

# Configurer le port Render
PORT="${PORT:-80}"
sed -i "s/^Listen .*/Listen $PORT/" /etc/apache2/ports.conf
sed -i "s/<VirtualHost \*:80>/<VirtualHost *:$PORT>/" /etc/apache2/sites-available/000-default.conf

echo "[SIRH] Migrations en cours..."
php artisan migrate --force || { echo "[ERREUR] Echec des migrations"; exit 1; }

echo "[SIRH] Seed des donnees de base..."
php artisan db:seed --force || { echo "[ERREUR] Echec du seed"; exit 1; }

chown -R www-data:www-data storage bootstrap/cache

echo "[SIRH] Apache demarre sur le port $PORT"
exec apache2-foreground
