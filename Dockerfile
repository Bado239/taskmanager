FROM php:8.2-fpm-alpine

# Installer les dépendances système, extensions PHP et Node.js/NPM
RUN apk add --no-cache nginx wget git unzip openssl bash postgresql-dev nodejs npm \
    && docker-php-ext-install pdo pdo_mysql pdo_pgsql

# Installer Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

WORKDIR /var/www/html
COPY . .

# Installer les dépendances du projet
RUN composer install --no-dev --optimize-autoloader --no-interaction

# Installer et compiler les assets (Vite/Tailwind/Alpine) pour le Dashboard
RUN npm install && npm run build

# Ajuster les permissions pour Laravel
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

# Configurer Nginx pour le port 10000
RUN echo 'server { \
    listen 10000; \
    root /var/www/html/public; \
    index index.php index.html; \
    location / { \
        try_files $uri $uri/ /index.php?$query_string; \
    } \
    location ~ \.php$ { \
        try_files $uri =404; \
        fastcgi_split_path_info ^(.+\.php)(/.+)$; \
        fastcgi_pass 127.0.0.1:9000; \
        fastcgi_index index.php; \
        include fastcgi_params; \
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name; \
        fastcgi_param PATH_INFO $fastcgi_path_info; \
    } \
}' > /etc/nginx/http.d/default.conf

EXPOSE 10000

# Lancer les migrations et démarrer les services
CMD php artisan migrate --force && php-fpm -D && nginx -g "daemon off;"