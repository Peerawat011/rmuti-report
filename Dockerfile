FROM tangramor/nginx-php8-fpm:php8.4.16_withoutNodejs

COPY . /var/www/html
WORKDIR /var/www/html

# Allow composer to run as root
ENV COMPOSER_ALLOW_SUPERUSER 1

# ติดตั้ง dependencies ตอน build — vendor ฝังใน image (บูต/ตื่นจาก sleep เร็ว)
RUN composer install --no-dev --optimize-autoloader --no-interaction

# ให้ php-fpm (ไม่ใช่ root) เขียน storage และ cache ได้ — ไม่งั้น Laravel boot ไม่ผ่าน (500 ทุกหน้า)
RUN chmod -R 777 /var/www/html/storage /var/www/html/bootstrap/cache

# สร้าง symlink public/storage ตั้งแต่ build — รูปอัปโหลดแสดงได้แม้สคริปต์บูตไม่ถูกรัน
RUN ln -sfn /var/www/html/storage/app/public /var/www/html/public/storage

# Image config
ENV WEBROOT /var/www/html/public
ENV RUN_SCRIPTS 0

# Laravel config
ENV APP_ENV production
ENV APP_DEBUG false
ENV LOG_CHANNEL stderr

# รันสคริปต์เตรียมระบบเองโดยตรง (ไม่พึ่ง RUN_SCRIPTS ของ image) แล้วค่อยสตาร์ท nginx/php-fpm
# ใช้ ; ไม่ใช่ && — ถ้า migrate พลาด (เช่น DB ล่มชั่วคราว) เว็บยังขึ้นให้ debug ได้
CMD ["/bin/bash", "-c", "bash /var/www/html/scripts/00-laravel-deploy.sh; exec /start.sh"]
