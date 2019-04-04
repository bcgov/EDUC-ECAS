FROM php:7.1-apache

# Add the necessary libraries and extensions to PHP
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

# Set the documet root
ENV APACHE_DOCUMENT_ROOT /web-app/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

# Enable mod_rewrite module to make our urls work
RUN a2enmod rewrite

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Create user ecas and add it to the root group.
RUN useradd -ms /bin/bash ecas
RUN usermod -a -G root ecas

RUN mkdir /web-app  \
    && chown -R ecas:root /web-app

USER ecas
WORKDIR /web-app

COPY ./web-app ./

RUN composer install

# OpenShift won't let us run as root user, but Apache won't start without it
# Need to find another solution
USER root

# We need write permission on some directories
# Don't know what that web server is running as, so just give access to everyone
# Probably not a good long term idea!
RUN chmod -R go+w ./storage
RUN chmod -R go+w ./bootstrap/cache