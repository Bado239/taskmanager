#!/usr/bin/env bash
composer install --no-dev --optimize-autoloader
php artisan migrate:fresh --force
php artisan db:seed --force
