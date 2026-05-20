#!/bin/sh
set -e

echo "[GoalFlow] Caching config..."
php artisan config:cache

echo "[GoalFlow] Caching routes..."
php artisan route:cache

echo "[GoalFlow] Caching views..."
php artisan view:cache

echo "[GoalFlow] Running migrations..."
php artisan migrate --force

echo "[GoalFlow] Creating storage symlink..."
php artisan storage:link --force 2>/dev/null || true

echo "[GoalFlow] Starting supervisord..."
exec /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf
