#!/bin/bash
# =============================================================================
# Docker Container Entrypoint — SkripsiTracker
# Runs once on every container startup before handing off to supervisord.
# =============================================================================
set -e

echo "🚀 SkripsiTracker — Container starting..."

# ---------------------------------------------------------------------------
# 1. Generate application key if not set
# ---------------------------------------------------------------------------
if [ -z "$APP_KEY" ]; then
    echo "⚙️  Generating application key..."
    php /var/www/html/artisan key:generate --force
fi

# ---------------------------------------------------------------------------
# 2. Ensure storage symlink exists (public disk)
# ---------------------------------------------------------------------------
echo "🔗 Creating storage symlink..."
php /var/www/html/artisan storage:link --force 2>/dev/null || true

# ---------------------------------------------------------------------------
# 3. Run database migrations (--force skips production confirmation prompt)
# ---------------------------------------------------------------------------
echo "🗄️  Running database migrations..."
php /var/www/html/artisan migrate --force

# ---------------------------------------------------------------------------
# 4. Cache Laravel configuration for maximum performance
# ---------------------------------------------------------------------------
echo "⚡ Caching config, routes, views..."
php /var/www/html/artisan config:cache
php /var/www/html/artisan route:cache
php /var/www/html/artisan view:cache
php /var/www/html/artisan event:cache

# ---------------------------------------------------------------------------
# 5. Set correct permissions (in case volumes are mounted at runtime)
# ---------------------------------------------------------------------------
echo "🔒 Setting directory permissions..."
chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

echo "✅ Startup complete. Handing off to supervisord..."
exec "$@"
