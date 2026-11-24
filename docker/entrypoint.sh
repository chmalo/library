#!/usr/bin/env sh
set -e

if [ "${1:-}" = "php" ] && [ "${2:-}" = "-S" ]; then
    if [ ! -f .env ]; then
        cp .env.example .env
    fi

    if [ ! -f vendor/autoload.php ]; then
        composer install --no-interaction --prefer-dist
    fi

    if ! grep -q '^APP_KEY=base64:' .env; then
        php artisan key:generate --ansi
    fi

    php artisan migrate --force
    php artisan l5-swagger:generate
    cd /app/public
fi

exec "$@"
