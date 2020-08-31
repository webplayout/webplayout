ARG PHP_VERSION=7.4.9
ARG NODE_VERSION=12.18.3
ARG NGINX_VERSION=1.19.2


FROM node:${NODE_VERSION}-alpine as webplayout_nodejs

WORKDIR /tmp

COPY assets ./assets

COPY package.json yarn.lock webpack.config.js .babelrc ./

RUN mkdir -p public/build

RUN set -eux; \
        yarn install; \
        yarn cache clean; \
        yarn build




FROM php:${PHP_VERSION}-fpm-alpine as webplayout_php

RUN docker-php-ext-install pdo_mysql bcmath

RUN apk add ffmpeg

WORKDIR /var/www

RUN echo "APP_ENV=prod" > .env
COPY bin/ bin/
COPY config/ config/
COPY src/ src/
COPY public/ public/
COPY templates/ templates/
COPY translations/ translations/
COPY tv.sql ./


COPY --from=composer /usr/bin/composer /usr/bin/composer

COPY composer.json composer.lock symfony.lock ./
RUN set -eux; \
    composer install --no-dev --prefer-dist --no-scripts --no-progress --no-suggest; \
    composer dump-env prod; \
    composer clear-cache; \
    mkdir -p var/cache var/log; \
    chmod -R 777 var/cache var/log


# RUN bin/console doctrine:schema:drop --force
# RUN bin/console doctrine:schema:update --force
# RUN bin/console doctrine:fixtures:load


RUN mkdir /var/www/media && chown www-data:www-data /var/www/media

RUN echo '*	*	*	*	*	/var/www/bin/console webplayout:schedule-worker' >> /var/spool/cron/crontabs/root

VOLUME ["/var/www/media"]

EXPOSE 9000

CMD ["php-fpm", "-F"]

COPY --from=webplayout_nodejs /tmp/public/build ./public/build
