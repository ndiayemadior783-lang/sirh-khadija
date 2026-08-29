#!/usr/bin/env bash
# =============================================================
#  Déploiement rapide du SIRH sur un VPS Ubuntu + Nginx + MySQL
#  Usage : exécuter sur le serveur fraîchement installé
# =============================================================
set -e

APP_DOMAIN="${1:-sirh.khadija-consulting.com}"
APP_DIR="/var/www/sirh-khadija"
GIT_URL="${2:-https://github.com/votre-compte/sirh-khadija.git}"

echo "==> Mise à jour du système"
sudo apt update && sudo apt upgrade -y

echo "==> Installation de PHP 8.3, Nginx, MySQL, Git, Composer, Node"
sudo apt install -y software-properties-common ca-certificates lsb-release
sudo add-apt-repository -y ppa:ondrej/php
sudo apt update
sudo apt install -y nginx mysql-server git unzip curl
sudo apt install -y php8.3-fpm php8.3-cli php8.3-mysql php8.3-sqlite3 \
    php8.3-mbstring php8.3-xml php8.3-curl php8.3-zip php8.3-intl php8.3-bcmath
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer
curl -fsSL https://deb.nodesource.com/setup_20.x | sudo -E bash -
sudo apt install -y nodejs

echo "==> Clonage du projet"
sudo mkdir -p "$APP_DIR"
sudo git clone "$GIT_URL" "$APP_DIR"
cd "$APP_DIR"

echo "==> Dépendances"
sudo composer install --no-dev --optimize-autoloader
npm install
npm run build

echo "==> Configuration .env"
sudo cp .env.example .env
sudo php artisan key:generate

echo "==> Création de la base MySQL"
sudo mysql -e "CREATE DATABASE IF NOT EXISTS sirh_khadija CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
sudo mysql -e "CREATE USER IF NOT EXISTS 'sirh'@'localhost' IDENTIFIED BY 'CHANGE_ME_SECURE_PASS';"
sudo mysql -e "GRANT ALL PRIVILEGES ON sirh_khadija.* TO 'sirh'@'localhost'; FLUSH PRIVILEGES;"

echo "==> Mise à jour de .env pour MySQL"
sudo sed -i 's/DB_CONNECTION=.*/DB_CONNECTION=mysql/' .env
sudo sed -i 's/# DB_HOST=.*/DB_HOST=127.0.0.1/' .env
sudo sed -i 's/# DB_PORT=.*/DB_PORT=3306/' .env
sudo sed -i 's/# DB_DATABASE=.*/DB_DATABASE=sirh_khadija/' .env
sudo sed -i 's/# DB_USERNAME=.*/DB_USERNAME=sirh/' .env
sudo sed -i 's/# DB_PASSWORD=.*/DB_PASSWORD=CHANGE_ME_SECURE_PASS/' .env
sudo sed -i "s|APP_URL=.*|APP_URL=https://$APP_DOMAIN|" .env
sudo sed -i 's/APP_ENV=.*/APP_ENV=production/' .env
sudo sed -i 's/APP_DEBUG=.*/APP_DEBUG=false/' .env

echo "==> Migration + données de démo"
sudo php artisan migrate --force
sudo php artisan db:seed --force

echo "==> Permissions"
sudo chown -R www-data:www-data storage bootstrap/cache
sudo chmod -R 775 storage bootstrap/cache

echo "==> Config Nginx"
sudo tee /etc/nginx/sites-available/sirh-khadija > /dev/null <<EOF
server {
    listen 80;
    server_name $APP_DOMAIN;
    root $APP_DIR/public;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";

    index index.php;
    charset utf-8;

    location / {
        try_files \$uri \$uri/ /index.php?\$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.3-fpm.sock;
        fastcgi_param SCRIPT_FILENAME \$realpath_root\$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
EOF
sudo ln -sf /etc/nginx/sites-available/sirh-khadija /etc/nginx/sites-enabled/
sudo nginx -t
sudo systemctl reload nginx

echo "==> HTTPS avec Certbot"
sudo apt install -y certbot python3-certbot-nginx
sudo certbot --nginx -d "$APP_DOMAIN" --non-interactive --agree-tos -m admin@khadijaconsulting.sn --redirect

echo "============================================"
echo "Déploiement terminé : https://$APP_DOMAIN"
echo "============================================"