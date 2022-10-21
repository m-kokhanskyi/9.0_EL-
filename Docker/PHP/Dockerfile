FROM php:8.1-fpm
RUN apt-get update

RUN mkdir -p /var/www/app

RUN pecl install redis --enable-redis-igbinary \  && docker-php-ext-enable redis

RUN apt-get install -y libzip-dev \
        &&  pecl install zip \
        && docker-php-ext-enable zip


# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/local/bin/composer

RUN chown -R www-data:www-data /var/www

USER www-data
