#!/bin/bash
set -e
cd "$(dirname "$0")/../.."

export APP_ENV=dev

composer install
vendor/bin/phinx migrate
vendor/bin/openapi src -o public/docs/openapi.yaml

HOST_PORT="${APP_PORT:-8000}"

echo "API: http://localhost:${HOST_PORT}"
php -c deploy/dev/php.ini -S "localhost:${HOST_PORT}" -t public
