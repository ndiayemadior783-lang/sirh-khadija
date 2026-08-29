#!/bin/sh
set -e

# Creer la base SQLite si absente (demo Render, disque persistant)
if [ ! -f database/database.sqlite ]; then
  touch database/database.sqlite && chown www-data:www-data database/database.sqlite || true
fi
PORT="${PORT:-80}"
sed -i "s/^Listen .*/Listen $PORT/" /etc/apache2/ports.conf
sed -i "s/<VirtualHost \*:80>/<VirtualHost *:$PORT>/" /etc/apache2/sites-available/000-default.conf
php artisan migrate --force
php artisan db:seed --force
exec apache2-foreground
