FROM composer:2 AS composer_deps

WORKDIR /app

COPY composer.json composer.lock ./
RUN composer install \
    --no-dev \
    --no-interaction \
    --prefer-dist \
    --optimize-autoloader \
    --no-scripts

FROM node:22-alpine AS frontend_build

WORKDIR /app

# Instalar PHP y extensiones requeridas por Laravel 12
RUN apk add --no-cache \
    php84 \
    php84-phar \
    php84-mbstring \
    php84-openssl \
    php84-tokenizer \
    php84-xml \
    php84-session \
    php84-fileinfo \
    php84-dom \
    php84-curl \
    php84-zip

# Crear enlace para usar php84 como php
RUN ln -sf /usr/bin/php84 /usr/bin/php

# Instalar Composer
RUN apk add --no-cache composer

COPY composer.json composer.lock ./
RUN composer install \
    --no-dev \
    --no-interaction \
    --prefer-dist \
    --optimize-autoloader \
    && rm -rf /var/cache/apk/*

COPY package.json package-lock.json ./
RUN npm ci

# Copiar vendor de Laravel
COPY --from=composer_deps /app/vendor ./vendor

# Generar rutas Wayfinder antes del build
RUN php artisan wayfinder:generate --with-form || true

COPY artisan ./
COPY app ./app
COPY bootstrap ./bootstrap
COPY config ./config
COPY database ./database
COPY routes ./routes
COPY resources ./resources
COPY public ./public
COPY vite.config.ts package.json tsconfig.json components.json ./

# Construir assets - ahora wayfinder ya fue generado
RUN npm run build

FROM php:8.4-apache

WORKDIR /var/www/html

RUN apt-get update && apt-get install -y \
    default-mysql-client \
    git \
    libicu-dev \
    libonig-dev \
    libzip-dev \
    unzip \
    && docker-php-ext-install bcmath intl pdo_mysql zip \
    && a2enmod rewrite \
    && rm -rf /var/lib/apt/lists/*

ENV APACHE_DOCUMENT_ROOT=/var/www/html/public

RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf \
    && sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

COPY --from=composer_deps /app/vendor ./vendor
COPY . .
COPY --from=frontend_build /app/public/build ./public/build
COPY docker/entrypoint.sh /usr/local/bin/taskhub-entrypoint

RUN chmod +x /usr/local/bin/taskhub-entrypoint \
    && chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

ENTRYPOINT ["taskhub-entrypoint"]
CMD ["apache2-foreground"]
