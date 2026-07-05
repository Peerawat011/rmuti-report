FROM tangramor/nginx-php8-fpm:php8.4.16_withoutNodejs

COPY . /var/www/html

# Image config
ENV WEBROOT /var/www/html/public
ENV RUN_SCRIPTS 1

# Laravel config
ENV APP_ENV production
ENV APP_DEBUG false
ENV LOG_CHANNEL stderr

# Allow composer to run as root
ENV COMPOSER_ALLOW_SUPERUSER 1

CMD ["/start.sh"]
