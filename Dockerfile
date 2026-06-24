# =============================================================================
# Stage 1: Composer Dependencies
# =============================================================================
FROM composer:2.8 AS composer-build

WORKDIR /app

# Copy only manifest files first to leverage Docker layer caching
COPY composer.json composer.lock ./

# Install production dependencies (no dev) without running scripts
RUN composer install \
    --no-dev \
    --no-scripts \
    --no-autoloader \
    --prefer-dist \
    --optimize-autoloader

# Copy the rest of the source code
COPY . .

# Generate optimized autoloader now that the full source is available
RUN composer dump-autoload --no-dev --optimize

# =============================================================================
# Stage 2: Node.js Frontend Build
# =============================================================================
FROM node:22-alpine AS node-build

WORKDIR /app

# Copy Node manifest files first for cache efficiency
COPY package.json package-lock.json ./

RUN npm ci --ignore-scripts

# Copy the full source to compile assets
COPY . .
# Pull vendor from previous stage so Vite plugins that read PHP config work
COPY --from=composer-build /app/vendor ./vendor

RUN npm run build

# =============================================================================
# Stage 3: Final Production Image
# =============================================================================
FROM php:8.5-fpm-alpine AS production

LABEL maintainer="SkripsiTracker <noreply@your-domain.com>"
LABEL description="SkripsiTracker — Thesis Progress Management System"
LABEL version="1.0.0"

# ---------------------------------------------------------------------------
# System Dependencies & PHP Extensions
# ---------------------------------------------------------------------------
RUN apk add --no-cache \
    # Runtime utilities
    bash \
    curl \
    git \
    nginx \
    supervisor \
    # PHP extension build dependencies
    libpng-dev \
    libjpeg-turbo-dev \
    freetype-dev \
    libzip-dev \
    icu-dev \
    oniguruma-dev \
    # MySQL client (for production DB)
    mysql-client \
 && docker-php-ext-configure gd \
      --with-freetype \
      --with-jpeg \
 && docker-php-ext-install -j$(nproc) \
      bcmath \
      exif \
      gd \
      intl \
      mbstring \
      opcache \
      pcntl \
      pdo_mysql \
      zip \
 # Install Redis extension via PECL
 && apk add --no-cache --virtual .build-deps $PHPIZE_DEPS \
 && pecl install redis \
 && docker-php-ext-enable redis \
 && apk del .build-deps \
 # Clean up image layer
 && rm -rf /var/cache/apk/* /tmp/*

# ---------------------------------------------------------------------------
# PHP Configuration
# ---------------------------------------------------------------------------
COPY docker/php/php.ini     /usr/local/etc/php/conf.d/app.ini
COPY docker/php/opcache.ini /usr/local/etc/php/conf.d/opcache.ini
COPY docker/php/fpm.conf    /usr/local/etc/php-fpm.d/www.conf

# ---------------------------------------------------------------------------
# Nginx Configuration
# ---------------------------------------------------------------------------
COPY docker/nginx/default.conf /etc/nginx/http.d/default.conf

# ---------------------------------------------------------------------------
# Supervisor Configuration (manages php-fpm + nginx + queue worker)
# ---------------------------------------------------------------------------
COPY docker/supervisor/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

# ---------------------------------------------------------------------------
# Application Source
# ---------------------------------------------------------------------------
WORKDIR /var/www/html

# Copy application source from earlier stages
COPY --from=composer-build /app                   .
COPY --from=composer-build /app/vendor            ./vendor
COPY --from=node-build     /app/public/build      ./public/build

# Ensure proper directory permissions
RUN chown -R www-data:www-data /var/www/html \
 && chmod -R 755 storage bootstrap/cache \
 && chmod -R 775 storage/app/public

# ---------------------------------------------------------------------------
# Container Entrypoint
# ---------------------------------------------------------------------------
COPY docker/entrypoint.sh /entrypoint.sh
RUN chmod +x /entrypoint.sh

# ---------------------------------------------------------------------------
# Metadata & Health Check
# ---------------------------------------------------------------------------
EXPOSE 80

HEALTHCHECK --interval=30s --timeout=10s --start-period=60s --retries=3 \
    CMD curl -f http://localhost/up || exit 1

ENTRYPOINT ["/entrypoint.sh"]
CMD ["/usr/bin/supervisord", "-c", "/etc/supervisor/conf.d/supervisord.conf"]
