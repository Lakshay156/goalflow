#!/bin/sh
set -e

echo "==> Starting GoalFlow deployment..."

# ── Storage symlink ─────────────────────────────────────────────
echo "==> Creating storage symlink..."
php artisan storage:link --force 2>/dev/null || true

# ── Caches ──────────────────────────────────────────────────────
echo "==> Caching config, routes, views..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

# ── Database ────────────────────────────────────────────────────
echo "==> Running migrations..."
php artisan migrate --force

# ── Permissions ─────────────────────────────────────────────────
echo "==> Setting permissions..."
chown -R www-data:www-data /var/www/html/storage
chown -R www-data:www-data /var/www/html/bootstrap/cache

echo "==> Starting services via supervisord..."
exec /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf
