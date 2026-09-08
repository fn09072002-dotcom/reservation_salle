#!/bin/sh
set -e

if [ ! -f /var/www/html/.env ]; then
    cat > /var/www/html/.env <<ENVEOF
APP_ENV=${APP_ENV:-production}
APP_DEBUG=${APP_DEBUG:-false}
DB_DRIVER=${DB_DRIVER:-mysql}
DB_HOST=${DB_HOST:-mysql}
DB_PORT=${DB_PORT:-3306}
DB_DATABASE=${DB_DATABASE:-reservation_salles}
DB_USERNAME=${DB_USERNAME:-fatou_app}
DB_PASSWORD=${DB_PASSWORD:-fatou123}
ENVEOF
fi

exec "$@"
