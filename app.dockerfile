FROM php:7.1-apache

ENV APACHE_DOCUMENT_ROOT /web-app/public

RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

#RUN apt-get update  \
#    && apt-get install -y \
#    libmcrypt-dev \
#    curl \
#    git \
#    zip \
#    unzip \
#    mysql-client libmagickwand-dev --no-install-recommends \
#    && pecl install imagick \
#    && docker-php-ext-enable imagick \
#    && docker-php-ext-install mcrypt pdo_mysql \
#    && docker-php-ext-install zip

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

WORKDIR /web-app

# copy project files and folders to the current working directory
COPY ./web-app ./
#COPY ./web-app/composer.json ./

RUN composer install --no-dev

EXPOSE 8080