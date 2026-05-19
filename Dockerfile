# Stage 1: scaffold Laravel 11 with composer
FROM composer:2 AS builder
WORKDIR /app
RUN composer create-project laravel/laravel:^11.0 . --no-dev --prefer-dist --no-interaction \
 && rm -rf .git \
 && composer require laravel/ui --no-interaction || true

# Stage 2: runtime
FROM php:8.4-apache

# System deps + PHP extensions
RUN apt-get update && apt-get install -y --no-install-recommends \
      git curl unzip libzip-dev libpng-dev libjpeg-dev libfreetype6-dev \
      libonig-dev libxml2-dev libicu-dev default-mysql-client \
 && docker-php-ext-configure gd --with-freetype --with-jpeg \
 && docker-php-ext-install -j"$(nproc)" \
      pdo_mysql mysqli mbstring exif pcntl bcmath gd zip intl xml \
 && rm -rf /var/lib/apt/lists/*

RUN a2enmod rewrite headers

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Apache docroot to public/
ENV APACHE_DOCUMENT_ROOT=/var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf \
 && sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf \
 && printf '<Directory ${APACHE_DOCUMENT_ROOT}>\n  AllowOverride All\n  Require all granted\n</Directory>\n' \
      > /etc/apache2/conf-available/laravel.conf \
 && a2enconf laravel

WORKDIR /var/www/html

# Copy scaffolded Laravel from builder
COPY --from=builder /app /var/www/html

# Overlay our custom code (replaces or adds files)
COPY overlay/ /var/www/html/

# Run composer install for any new package needs (none atm) and dump autoload
RUN composer dump-autoload --optimize --no-interaction

RUN chown -R www-data:www-data /var/www/html \
 && chmod -R 775 storage bootstrap/cache

COPY docker/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

EXPOSE 80
ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]
CMD ["apache2-foreground"]
