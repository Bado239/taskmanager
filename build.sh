#!/usr/bin/env bash
composer install --no-dev --optimize-autoloader
php artisan migrate:fresh --force
php artisan db:seed --force


git add .
git commit -m "Mise à jour de la vue Blade et du controleur"
git push origin main