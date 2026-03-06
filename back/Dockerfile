FROM php:8.4-fpm-alpine AS base

USER root

RUN apk update && apk add --no-cache \
    git \
    curl \
    curl-dev \
    ca-certificates \
    libpng-dev \
    libxml2-dev \
    zip \
    unzip \
    libzip-dev \
    mysql-client \
    mariadb-connector-c-dev \
    freetype-dev \
    libjpeg-turbo-dev \
    libwebp-dev \
    icu-dev \
    oniguruma-dev \
    ghostscript \
    bash \
    imagemagick \
    imagemagick-dev \
    autoconf \
    gcc \
    g++ \
    make \
    pkgconfig \
    openssl-dev \
    && update-ca-certificates

RUN docker-php-ext-configure gd --with-freetype --with-jpeg --with-webp \
    && docker-php-ext-install \
        pdo \
        pdo_mysql \
        mysqli \
        mbstring \
        exif \
        pcntl \
        bcmath \
        gd \
        zip \
        intl \
        opcache \
        soap \
        xml \
        xmlwriter

RUN apk add --no-cache imagemagick-dev imagemagick && \
    pecl install imagick && \
    echo "extension=imagick.so" > /usr/local/etc/php/conf.d/imagick.ini && \
    php -m | grep -i imagick

RUN pecl install redis && \
    echo "extension=redis.so" > /usr/local/etc/php/conf.d/redis.ini && \
    php -m | grep -i redis

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

RUN addgroup -g 1000 www 2>/dev/null || true && \
    adduser -u 1000 -G www -s /bin/bash -D www 2>/dev/null || true

RUN apk update && apk add --no-cache supervisor && \
    mkdir -p /var/log/supervisor && \
    mkdir -p /var/run && \
    chown -R www:www /var/log/supervisor

COPY ./docker/php/php.ini /usr/local/etc/php/php.ini

COPY composer.json ./
COPY composer.lock* ./

WORKDIR /var/www/html

FROM base AS development

USER root

RUN apk update && apk add --no-cache \
    git \
    curl \
    supervisor \
    ghostscript

USER www

USER root
RUN chown www:www composer.lock
USER www
RUN composer update --no-interaction --no-scripts

COPY ./docker/supervisor/supervisord.conf /etc/supervisor/conf.d/supervisord.conf
COPY ./docker/php/php.ini /usr/local/etc/php/php.ini

EXPOSE 9000

CMD ["/usr/bin/supervisord", "-c", "/etc/supervisor/conf.d/supervisord.conf"]

FROM base AS production

COPY ./docker/supervisor/supervisord.conf /etc/supervisor/conf.d/supervisord.conf
COPY ./docker/php/php.ini /usr/local/etc/php/php.ini
COPY ./docker/scripts/ /var/www/html/docker/scripts/

RUN chmod +x /var/www/html/docker/scripts/*.sh

RUN composer install --no-dev --no-interaction --optimize-autoloader --no-scripts && \
    php -m | grep -i imagick && \
    php -r "echo 'Imagick class exists: ' . (class_exists('Imagick') ? 'YES' : 'NO') . PHP_EOL;"

USER root

RUN mkdir -p /var/www/html/storage/logs /var/run /var/log/supervisor && \
    chown -R www:www /var/www/html/storage /var/run /var/log/supervisor && \
    chmod -R 775 /var/www/html/storage && \
    chmod 755 /var/run && \
    chmod 755 /var/log/supervisor && \
    chmod +x /var/www/html/docker/scripts/fix-permissions.sh

RUN mkdir -p /var/run/php

EXPOSE 9000

CMD ["/usr/bin/supervisord", "-c", "/etc/supervisor/conf.d/supervisord.conf"]
