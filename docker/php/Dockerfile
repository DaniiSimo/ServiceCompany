FROM composer:2 AS composer
WORKDIR /app
COPY composer.json composer.lock ./
RUN composer install --prefer-dist --optimize-autoloader --no-scripts

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

RUN sed -ri 's|^listen\s*=.*$|listen = 9000|' /usr/local/etc/php-fpm.d/www.conf \
 && sed -ri 's|^user\s*=.*$|user = www|'       /usr/local/etc/php-fpm.d/www.conf \
 && sed -ri 's|^group\s*=.*$|group = www|'     /usr/local/etc/php-fpm.d/www.conf \
 && sed -ri 's|^;?listen.owner\s*=.*$|listen.owner = www|' /usr/local/etc/php-fpm.d/www.conf \
 && sed -ri 's|^;?listen.group\s*=.*$|listen.group = www|' /usr/local/etc/php-fpm.d/www.conf

# Создаем пользователя и группу раньше, чтобы использовать --chown в COPY
RUN addgroup -g 1000 www \
    && adduser -G www -u 1000 -D www

COPY docker/php/entrypoint.sh /usr/local/bin/entrypoint.sh

# Копируем код и vendor с chown на build-time (это устраняет рекурсивный chown в runtime)
COPY --chown=www:www ./ .
COPY --chown=www:www --from=composer /app/vendor ./vendor

RUN chmod +x /usr/local/bin/entrypoint.sh \
    && chmod -R ug+rwX "./storage" "./bootstrap/cache" \
    && php artisan package:discover --ansi

ENTRYPOINT ["entrypoint.sh"]
CMD ["php-fpm", "-F"]
