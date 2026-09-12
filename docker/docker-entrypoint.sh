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

# Force un seul MPM actif (contournement d'un probleme de cache de
# build observe sur Railway ou mpm_event restait active malgre
# a2dismod au moment du build). Fait au demarrage, garantit
# l'etat correct quel que soit ce qui s'est passe pendant le build.
rm -f /etc/apache2/mods-enabled/mpm_event.load /etc/apache2/mods-enabled/mpm_event.conf
rm -f /etc/apache2/mods-enabled/mpm_worker.load /etc/apache2/mods-enabled/mpm_worker.conf
ln -sf /etc/apache2/mods-available/mpm_prefork.load /etc/apache2/mods-enabled/mpm_prefork.load
ln -sf /etc/apache2/mods-available/mpm_prefork.conf /etc/apache2/mods-enabled/mpm_prefork.conf

echo "=== Modules MPM apres correction ==="
ls -la /etc/apache2/mods-enabled/ | grep -i mpm
echo "======================================"

exec "$@"
