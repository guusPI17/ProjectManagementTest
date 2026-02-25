#!/bin/bash
set -e
cd "$(dirname "$0")/../.."

export APP_ENV=dev

composer install
vendor/bin/openapi src -o public/docs/openapi.yaml

composer db:create
composer db:migrate

HOST_PORT="${APP_PORT:-8000}"

echo "API: http://localhost:${HOST_PORT}"
php -c deploy/dev/php.ini -S "localhost:${HOST_PORT}" -t public
