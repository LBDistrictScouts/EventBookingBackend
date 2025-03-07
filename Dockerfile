FROM php:8.3-fpm-alpine
LABEL authors="Jacob Tyler, Letchworth, Baldock & Ashwell Scouts"

ARG user=www-data
ARG group=www-data
#ARG uid=969

RUN apk update
RUN apk add bash zip unzip postgresql17-client icu-dev libpq-dev php83-pdo_pgsql php83-pgsql

RUN docker-php-ext-configure intl
RUN docker-php-ext-configure pgsql
RUN docker-php-ext-install intl pgsql pdo pdo_pgsql

COPY --from=composer:latest /usr/bin/composer /usr/local/bin/composer

RUN mkdir -p /home/$user/.composer \
    && chown -R $user:$group /home/$user

USER $user

WORKDIR /var/www/html

COPY composer.json .
COPY composer.lock .

RUN composer install --optimize-autoloader --no-scripts --no-interaction --profile --version

COPY . .

USER root
RUN chown -R $user:$group /var/www/html \
    && chmod -R 777 /var/www/html
USER $user
