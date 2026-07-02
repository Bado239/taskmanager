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
    postgresql-dev \
    libpng-dev \
    libzip-dev \
    oniguruma-dev

# =========================
# 2. Extensions PHP
# =========================
RUN docker-php-ext-install \
    pdo \
    pdo_mysql \
    pdo_pgsql \
    mbstring \
    zip \
    gd \
    exif

# =========================
# 3. Composer
# =========================
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# =========================
# 4. Working directory
# =========================
WORKDIR /var/www/html

# =========================
# 5. Copier projet
# =========================
COPY . .

# =========================
# 6. Installer dépendances Laravel
# =========================
RUN composer install \
    --no-dev \
    --optimize-autoloader \
    --no-interaction

# =========================
# 7. Permissions Laravel
# =========================
RUN chown -R www-data:www-data \
    /var/www/html/storage \
    /var/www/html/bootstrap/cache

# =========================
# 8. Config Nginx
# =========================
RUN mkdir -p /etc/nginx/http.d && \
    echo 'server {
    listen 80;
    root /var/www/html/public;
    index index.php index.html;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        try_files $uri =404;
        fastcgi_pass 127.0.0.1:9000;
        fastcgi_index index.php;
        include fastcgi_params;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
    }
}' > /etc/nginx/http.d/default.conf

# =========================
# 9. Port
# =========================
EXPOSE 80

# =========================
# 10. Startup script propre
# =========================
CMD sh -c "php-fpm -D && nginx -g 'daemon off;'"