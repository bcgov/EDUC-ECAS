FROM php:7.1-fpm

RUN apt-get update  \
    && apt-get install -y \
    libmcrypt-dev \
    curl \
    git \
    zip \
    unzip \
    mysql-client libmagickwand-dev --no-install-recommends \
    && pecl install imagick \
    && docker-php-ext-enable imagick \
    && docker-php-ext-install mcrypt pdo_mysql \
    && docker-php-ext-install zip

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

WORKDIR /

# copy project files and folders to the current working directory
COPY ./web-app ./

# COPY ./web-app/composer.json ./

RUN composer install