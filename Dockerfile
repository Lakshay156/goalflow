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
    dos2unix \
    libpng-dev \
    libjpeg-turbo-dev \
    freetype-dev \
    oniguruma-dev \
    postgresql16-dev \
    supervisor \
    shadow

# ── PHP extensions ──────────────────────────────────────────────
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install \
        pdo \
        pdo_pgsql \
        gd \
        opcache \
        bcmath \
        mbstring \
        pcntl

# ── Composer ────────────────────────────────────────────────────
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# ── App source ─────────────────────────────────────────────────
WORKDIR /var/www/html

# Copy composer files first for layer caching
COPY composer.json composer.lock ./
RUN composer install --no-dev --no-interaction --optimize-autoloader --no-scripts --no-autoloader

# Copy rest of app
COPY . .

# Finish composer autoloader
RUN composer dump-autoload --no-dev --optimize

# Build frontend assets — then remove dev-server hints
RUN npm ci && npm run build && rm -rf node_modules && rm -f public/hot

# ── Permissions ─────────────────────────────────────────────────
RUN mkdir -p storage/framework/{sessions,views,cache} \
    && mkdir -p /var/log/supervisor \
    && chown -R www-data:www-data /var/www/html \
    && chmod -R 755 storage bootstrap/cache

# ── Configs ─────────────────────────────────────────────────────
COPY docker/nginx.conf /etc/nginx/nginx.conf
COPY docker/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

# Fix Windows line endings on startup script, then make executable
COPY docker/start.sh /start.sh
RUN dos2unix /start.sh && chmod +x /start.sh

EXPOSE 10000

CMD ["/start.sh"]
