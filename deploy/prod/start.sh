#!/bin/bash
set -e

PROJECT_DIR="$(cd "$(dirname "$0")/../.." && pwd)"

export APP_ENV=prod

cd "$PROJECT_DIR"
composer install # Вначале устанавливаем с dev, чтобы сгенерировать OpenAPI
vendor/bin/openapi src -o public/docs/openapi.yaml
composer install --no-dev --optimize-autoloader --classmap-authoritative

vendor/bin/phinx migrate

mkdir -p var/log

sed "s|/path/to/project|${PROJECT_DIR}|g" deploy/prod/nginx.conf \
    | sudo tee /etc/nginx/sites-available/project-management > /dev/null
sed "s|/path/to/project|${PROJECT_DIR}|g" deploy/prod/php-fpm-pool.conf \
    | sudo tee /etc/php/8.3/fpm/pool.d/project-management.conf > /dev/null
sed "s|/path/to/project|${PROJECT_DIR}|g" deploy/prod/php.ini \
    | sudo tee /etc/php/8.3/fpm/conf.d/99-project-management.ini > /dev/null

sudo ln -sf /etc/nginx/sites-available/project-management /etc/nginx/sites-enabled/
sudo nginx -t && sudo systemctl reload nginx

sudo systemctl reload php8.3-fpm

echo "Prod развёрнут: ${PROJECT_DIR}"
echo "  nginx:      перезагружен"
echo "  php-fpm:    перезагружен"
