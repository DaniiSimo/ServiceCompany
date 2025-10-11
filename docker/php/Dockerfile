FROM composer:2 AS composer

FROM php:8.3-fpm-alpine AS app
WORKDIR /var/www/html

RUN apk add --no-cache \
        $PHPIZE_DEPS \
        zlib-dev \
        libzip-dev \
        libpng-dev \
        libjpeg-turbo-dev \
        freetype-dev \
        oniguruma-dev \
        libxml2-dev \
        icu-dev \
        gettext-dev \
        postgresql-dev \
        zip

RUN docker-php-ext-configure gd \
        --with-freetype=/usr/include/ \
        --with-jpeg=/usr/include/ \
    && docker-php-ext-install -j$(nproc) \
        gd pcntl bcmath mysqli pdo_mysql pdo_pgsql pgsql opcache intl zip soap \
    && pecl install redis \
    && docker-php-ext-enable redis

RUN rm -rf /var/cache/apk/* /tmp/* /var/tmp/*


ARG BUILD_APP=0

RUN sed -ri 's|^listen\s*=\s*.+$|listen = 9000|' /usr/local/etc/php-fpm.d/www.conf

COPY --from=composer /usr/bin/composer /usr/local/bin/composer
COPY docker/php/entrypoint.sh /usr/local/bin/entrypoint.sh
COPY ./ /var/www/html

RUN chmod +x /usr/local/bin/entrypoint.sh \
    && addgroup -g 1000 www \
    && adduser  -G www -u 1000 -D www

ENTRYPOINT ["entrypoint.sh"]
CMD ["php-fpm", "-F"]
