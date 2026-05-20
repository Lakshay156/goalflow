FROM php:8.2-fpm-alpine

# ── System deps ────────────────────────────────────────────────
RUN apk add --no-cache \
    nginx \
    nodejs \
    npm \
    git \
    curl \
    zip \
    unzip \
    libpng-dev \
    libjpeg-turbo-dev \
    freetype-dev \
    oniguruma-dev \
    postgresql-dev \
    supervisor

# ── PHP extensions ──────────────────────────────────────────────
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install \
        pdo \
        pdo_pgsql \
        pgsql \
        gd \
        opcache \
        bcmath \
        mbstring \
        exif \
        pcntl

# ── Composer ────────────────────────────────────────────────────
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# ── App ─────────────────────────────────────────────────────────
WORKDIR /var/www/html

COPY . .

# Install PHP deps (no dev)
RUN composer install --no-dev --no-interaction --optimize-autoloader --no-scripts

# Install Node deps & build assets
RUN npm ci && npm run build

# Permissions
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html/storage \
    && chmod -R 755 /var/www/html/bootstrap/cache

# ── Config ──────────────────────────────────────────────────────
COPY docker/nginx.conf      /etc/nginx/nginx.conf
COPY docker/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

# ── Startup ─────────────────────────────────────────────────────
COPY docker/start.sh /start.sh
RUN chmod +x /start.sh

EXPOSE 10000

CMD ["/start.sh"]
