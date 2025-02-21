FROM php:7.4-fpm-alpine

LABEL Organization="qsnctf" Author="M0x1n <lqn@sierting.com>"

COPY files /tmp/

COPY www /var/www/html/


RUN sed -i 's/dl-cdn.alpinelinux.org/mirrors.nju.edu.cn/g' /etc/apk/repositories \
    && echo "---------- Install exif ----------" \
    && docker-php-ext-install -j 2 exif \
    && apk add --update --no-cache nginx \
    && mkdir -p /run/nginx \
    && mkdir -p /var/log/nginx \
    # configure file
    && mv /tmp/flag.sh /flag.sh \
    && mv /tmp/docker-entrypoint /usr/local/bin/docker-entrypoint \
    && mv /tmp/nginx.conf /etc/nginx/nginx.conf \
    && mv /tmp/php.ini /usr/local/etc/php/php.ini-development \
    && cp /usr/local/etc/php/php.ini-development /usr/local/etc/php/php.ini-production \
    && cp /usr/local/etc/php/php.ini-development /usr/local/etc/php/php.ini \
    && mkdir /var/www/html/uploads \
    && chmod 777 /var/www/html/uploads \
    && chown -R www-data:www-data /var/www/html \
    && chown -R www-data:www-data /var/www/html/uploads \
    && chmod +x /usr/local/bin/docker-entrypoint \
    # clear
    && rm -rf /tmp/*
    # && rm -rf /etc/apk

WORKDIR /var/www/html

EXPOSE 80

VOLUME ["/var/log/nginx"]

CMD ["/bin/sh", "-c", "docker-entrypoint"]