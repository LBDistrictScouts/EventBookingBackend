ARG PHP_VERSION=8.5
FROM php:${PHP_VERSION}-fpm-alpine
LABEL authors="Jacob Tyler, Letchworth, Baldock & Ashwell Scouts"

ARG user=www-data
ARG group=www-data
#ARG uid=969

ENV TAR_OPTIONS="--no-same-owner"

WORKDIR /tmp

RUN apk update
RUN apk add bash zip unzip nodejs npm yarn postgresql17-client icu-dev libpq-dev php85-pdo_pgsql php85-pgsql

RUN docker-php-ext-configure intl || true
RUN docker-php-ext-configure pgsql
RUN docker-php-ext-install intl pgsql pdo pdo_pgsql

COPY --from=composer:latest /usr/bin/composer /usr/local/bin/composer

RUN mkdir -p /home/$user/.composer \
    && chown -R $user:$group /home/$user

USER $user

WORKDIR /var/www/html

COPY composer.json .
COPY composer.lock .

RUN composer install --optimize-autoloader --no-scripts --no-interaction --profile --prefer-dist

COPY . .

USER root
RUN chown -R $user:$group /var/www/html \
    && chmod -R 777 /var/www/html
USER $user

RUN ./bin/cake district_ui install --overwrite
