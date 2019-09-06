FROM nginx:alpine

COPY conf.d /etc/nginx/conf.d

COPY --from=webplayout/php_fpm /var/www /var/www

VOLUME ["/var/www/media", "/var/www/hls_live"]

EXPOSE 80
