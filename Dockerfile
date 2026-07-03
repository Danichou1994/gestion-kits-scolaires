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
RUN if [ -f /var/www/html/.env.example ]; then \
        cp /var/www/html/.env.example /var/www/html/.env; \
    else \
        echo "APP_ENV=production" > /var/www/html/.env; \
        echo "APP_DEBUG=false" >> /var/www/html/.env; \
        echo "APP_KEY=" >> /var/www/html/.env; \
        echo "DB_CONNECTION=pgsql" >> /var/www/html/.env; \
    fi

# Installer les dépendances
RUN composer install --no-dev --prefer-dist --ignore-platform-req=php

# Configurer les permissions
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html/storage \
    && chmod -R 755 /var/www/html/bootstrap/cache \
    && chmod -R 777 /var/www/html

# Générer la clé d'application (utilisera les variables d'env)
RUN php artisan key:generate

EXPOSE 80