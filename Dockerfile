# Stage 1: Build PHP dependencies
FROM composer:2.7 AS vendor
WORKDIR /app
COPY composer.json composer.lock ./
# Install dependencies but skip scripts/autoloader for now
RUN composer install --no-dev --no-scripts --no-autoloader --prefer-dist --ignore-platform-reqs

# Stage 2: Build frontend assets
FROM node:20 AS frontend
WORKDIR /app
COPY package.json package-lock.json ./
RUN npm ci
COPY vite.config.js tailwind.config.js postcss.config.js ./
COPY resources resources
RUN npm run build

# Stage 3: Final Production Image
FROM serversideup/php:8.2-fpm-nginx

# Set the document root for Nginx
ENV WEB_DOCUMENT_ROOT=/var/www/html/public
ENV PHP_OPCACHE_ENABLE=1

# Switch to root to install PostgreSQL client extension and configure permissions
USER root
RUN apt-get update \
    && apt-get install -y php8.2-pgsql \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Switch back to the non-root user that comes with the image
USER www-data
WORKDIR /var/www/html

# Copy the application code
COPY . .

# Copy built vendor and public/build directories from previous stages
COPY --from=vendor /app/vendor/ vendor/
COPY --from=frontend /app/public/build/ public/build/

# Generate autoloader, run scripts, and ensure proper ownership
USER root
RUN composer dump-autoload --optimize \
    && chown -R www-data:www-data /var/www/html

# Run as www-data
USER www-data
