ARG PHP_VERSION=7.3
ARG NODE_VERSION=10
ARG NGINX_VERSION=1.16

FROM php:${PHP_VERSION}-fpm-alpine

WORKDIR /var/www

#COPY . /var/www

RUN rm -rf /var/www/html

COPY --from=composer /usr/bin/composer /usr/bin/composer

# Add and Enable PHP-PDO Extenstions
RUN docker-php-ext-install pdo_mysql bcmath

#     php7-gd \
#     php7-intl \
#     php7-mcrypt \
#     php7-opcache \
#     php7-soap \
#     php7-zip \


#RUN apk add shadow && usermod -u 1000 www-data && groupmod -g 1000 www-data

VOLUME /var/www/media

EXPOSE 9000

CMD ["php-fpm", "-F"]
