# Multi-stage build for production optimization
FROM php:8.2-fpm-alpine AS base

# Install system dependencies
RUN apk add --no-cache \
    git \
    curl \
    libpng-dev \
    libxml2-dev \
    zip \
    unzip \
    oniguruma-dev \
    icu-dev \
    freetype-dev \
    libjpeg-turbo-dev \
    libzip-dev \
    mysql-client \
    nginx \
    supervisor

# Install PHP extensions
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install \
        pdo_mysql \
        mbstring \
        exif \
        pcntl \
        bcmath \
        gd \
        zip \
        intl \
        xml \
        soap

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www/html

# Development stage
FROM base AS development

# Install Xdebug for development
RUN pecl install xdebug \
    && docker-php-ext-enable xdebug

# Copy Xdebug configuration
COPY docker/php/xdebug.ini /usr/local/etc/php/conf.d/xdebug.ini

# Copy PHP configuration for development
COPY docker/php/php-dev.ini /usr/local/etc/php/conf.d/php.ini

# Copy application code
COPY . .

# Install dependencies including dev dependencies
RUN composer install --no-scripts --no-autoloader

# Generate autoloader
RUN composer dump-autoload --optimize

# Set permissions
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html/storage \
    && chmod -R 755 /var/www/html/uploads

# Production stage
FROM base AS production

# Copy PHP configuration for production
COPY docker/php/php-prod.ini /usr/local/etc/php/conf.d/php.ini

# Copy Nginx configuration
COPY docker/nginx/nginx.conf /etc/nginx/nginx.conf
COPY docker/nginx/default.conf /etc/nginx/conf.d/default.conf

# Copy Supervisor configuration
COPY docker/supervisor/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

# Copy application code
COPY --chown=www-data:www-data . .

# Install production dependencies only
RUN composer install --no-dev --no-scripts --no-autoloader --optimize-autoloader

# Generate optimized autoloader
RUN composer dump-autoload --optimize --classmap-authoritative

# Remove composer and unnecessary files
RUN rm -rf /usr/bin/composer \
    && rm -rf /var/cache/apk/* \
    && rm -rf /tmp/* \
    && rm -rf .git \
    && rm -rf tests \
    && rm -rf .github \
    && rm -rf docker \
    && rm -f .env.example \
    && rm -f phpunit.xml \
    && rm -f phpstan.neon \
    && rm -f .php-cs-fixer.php \
    && rm -f README.md

# Set permissions
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html/storage \
    && chmod -R 755 /var/www/html/uploads \
    && chmod -R 644 /var/www/html/app \
    && chmod +x /var/www/html/console.php

# Create necessary directories
RUN mkdir -p /var/log/nginx \
    && mkdir -p /var/log/supervisor \
    && mkdir -p /run/nginx

# Health check
HEALTHCHECK --interval=30s --timeout=3s --start-period=5s --retries=3 \
    CMD curl -f http://localhost/health || exit 1

# Expose port
EXPOSE 80

# Start supervisor
CMD ["/usr/bin/supervisord", "-c", "/etc/supervisor/conf.d/supervisord.conf"]

# Labels for metadata
LABEL maintainer="StyloFitness Team" \
      version="1.0" \
      description="StyloFitness PHP Application" \
      org.opencontainers.image.source="https://github.com/stylofitness/stylofitness"