#!/usr/bin/env bash
# งานเตรียมระบบตอนบูต (composer ทำไปแล้วตอน build image)

echo "==> Caching config, routes & views..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "==> Linking storage..."
php artisan storage:link

echo "==> Running migrations..."
php artisan migrate --force

echo "==> Seeding admin account (create if missing)..."
php artisan db:seed --class=AdminSeeder --force

echo "==> Laravel deploy script finished."
