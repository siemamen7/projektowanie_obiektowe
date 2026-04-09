FROM kprzystalski/projobj-php:latest

WORKDIR /home/student/projobj

COPY composer.json composer.lock ./

RUN composer install --no-dev --optimize-autoloader --classmap-authoritative --no-interaction

COPY . .

RUN php bin/console doctrine:migrations:migrate --no-interaction --env=prod

RUN php bin/console cache:warmup --env=prod --no-debug

ENV APP_ENV=prod
ENV APP_DEBUG=0

EXPOSE 8000
CMD ["php-fpm"]