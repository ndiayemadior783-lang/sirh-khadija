#!/bin/sh
set -e
PORT="${PORT:-80}"
sed -i "s/^Listen .*/Listen $PORT/" /etc/apache2/ports.conf
sed -i "s/<VirtualHost \*:80>/<VirtualHost *:$PORT>/" /etc/apache2/sites-available/000-default.conf
php artisan migrate --force
php artisan db:seed --force
exec apache2-foreground
