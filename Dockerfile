FROM php:8.2-fpm-alpine

# =========================
# 1. Dépendances système
# =========================
RUN apk add --no-cache \
    nginx \
    bash \
    git \
    unzip \
    curl \
    openssl \
    postgresql-dev

# =========================
# 2. Extensions PHP (IMPORTANT ordre + build deps)
# =========================
RUN docker-php-ext-install pdo pdo_mysql

RUN docker-php-ext-install pdo_pgsql

# =========================
# 3. Vérification (DEBUG utile)
# =========================
RUN php -m | grep pdo_pgsql

# =========================
# 4. Composer
# =========================
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# =========================
# 5. App
# =========================
WORKDIR /var/www/html
COPY . .

# =========================
# 6. Composer install
# =========================
RUN composer install --no-dev --optimize-autoloader --no-interaction

# =========================
# 7. Permissions
# =========================
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

# =========================
# 8. Nginx config
# =========================
RUN mkdir -p /etc/nginx/http.d && \
    echo 'server {
    listen 80;
    root /var/www/html/public;
    index index.php;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        include fastcgi_params;
        fastcgi_pass 127.0.0.1:9000;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
    }
}' > /etc/nginx/http.d/default.conf

EXPOSE 80

CMD sh -c "php-fpm -D && nginx -g 'daemon off;'"