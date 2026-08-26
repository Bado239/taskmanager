FROM php:8.2-fpm

# 1. Installation des dépendances système et Node.js
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libpq-dev \
    zip \
    unzip \
    nodejs \
    npm

# 2. Installation des extensions PHP (avec PostgreSQL)
RUN docker-php-ext-install pdo pdo_pgsql mbstring exif pcntl bcmath gd


# 2 BIS. Configuration upload gros fichiers PDF
RUN echo "upload_max_filesize=100M" > /usr/local/etc/php/conf.d/uploads.ini \
 && echo "post_max_size=120M" >> /usr/local/etc/php/conf.d/uploads.ini \
 && echo "memory_limit=256M" >> /usr/local/etc/php/conf.d/uploads.ini \
 && echo "max_execution_time=300" >> /usr/local/etc/php/conf.d/uploads.ini \
 && echo "max_input_time=300" >> /usr/local/etc/php/conf.d/uploads.ini

# 3. Dossier de travail
WORKDIR /var/www

# 4. Copie du projet
COPY . .

# 5. Installation de Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
RUN composer install --no-dev --optimize-autoloader

# 6. COMPILATION DE TAILWIND CSS (Crucial pour ton design !)
RUN npm install
RUN npm run build

# 7. Donner les permissions
RUN chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache

EXPOSE 8000

# 8. Commande de démarrage (remplace le port/command selon ton setup docker)
CMD php artisan migrate --force && php artisan serve --host=0.0.0.0 --port=$PORT