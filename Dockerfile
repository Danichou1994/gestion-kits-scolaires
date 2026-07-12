FROM php:8.4-apache

# Installer les extensions nécessaires
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    libzip-dev \
    libpq-dev \
    && docker-php-ext-install pdo_mysql pdo_pgsql mbstring exif pcntl bcmath gd zip

# Installer Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Activer Apache mod_rewrite
RUN a2enmod rewrite

# Configurer le DocumentRoot pour Laravel
ENV APACHE_DOCUMENT_ROOT /var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

# Copier les fichiers du projet
COPY . /var/www/html

# Créer le fichier .env à partir des variables d'environnement
RUN echo "APP_ENV=production" > /var/www/html/.env && \
    echo "APP_DEBUG=false" >> /var/www/html/.env && \
    echo "APP_KEY=base64:cpGpFhl4Nq07+oKVN+uDXnVyRof6qJE7dL2UhJW1vaU=" >> /var/www/html/.env && \
    echo "DB_CONNECTION=pgsql" >> /var/www/html/.env && \
    echo "DB_HOST=dpg-d93oddcvikkc73anslp0-a" >> /var/www/html/.env && \
    echo "DB_PORT=5432" >> /var/www/html/.env && \
    echo "DB_DATABASE=gestion_kits_db" >> /var/www/html/.env && \
    echo "DB_USERNAME=gestion_kits_db_user" >> /var/www/html/.env && \
    echo "DB_PASSWORD=0CshgDZQZ0tGDqXx

# Installer les dépendances
RUN composer install --no-dev --prefer-dist --ignore-platform-req=php

# Exécuter les migrations
RUN php artisan migrate --force

# Configurer les permissions
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html/storage \
    && chmod -R 755 /var/www/html/bootstrap/cache

EXPOSE 80