#!/usr/bin/env bash
echo "Running composer..."
composer install --no-dev --optimize-autoloader --working-dir=/var/www/html

echo "Caching config, routes & views..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "Linking storage..."
php artisan storage:link

echo "Running migrations..."
php artisan migrate --force

echo "Seeding admin account (create if missing)..."
php artisan db:seed --class=AdminSeeder --force
