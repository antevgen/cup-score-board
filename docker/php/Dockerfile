FROM php:8.3-fpm-alpine

# System deps
RUN apk add --no-cache bash git unzip icu-dev libpq-dev linux-headers $PHPIZE_DEPS

# PHP extensions
RUN docker-php-ext-install intl pdo pdo_pgsql opcache

# Xdebug
RUN pecl install xdebug \
 && docker-php-ext-enable xdebug

# Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# PHP config
COPY conf.d/php.ini /usr/local/etc/php/conf.d/php.ini
COPY conf.d/xdebug.ini /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini

WORKDIR /var/www/html
