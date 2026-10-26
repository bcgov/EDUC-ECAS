# ECAS frontend (web-app) image for running the Laravel/Vue app locally.
#
# Mirrors the OpenShift S2I build (registry.redhat.io/rhscl/php-73-rhel7 plus
# web-app/php-post-assemble/npm-install.sh) using public base images, so no Red Hat
# registry login is needed. Build from the repository root:
#
#   docker compose -f docker/docker-compose.frontend.yml build
#
# PHP_VERSION should match the PHP release of the S2I builder image.

ARG PHP_VERSION=8.3
ARG NODE_VERSION=14
ARG COMPOSER_VERSION=2.10

# --- Vue/Sass assets: laravel-mix 4 (webpack 4) needs an older Node ---
FROM node:${NODE_VERSION}-bullseye AS assets
WORKDIR /app
COPY web-app/package.json web-app/package-lock.json ./
# npm install, not npm ci, as in php-post-assemble/npm-install.sh: package-lock.json is out of sync with package.json.
RUN npm install --no-audit --no-fund
# laravel-mix only writes into public/ (fonts, mix-manifest.json) when it finds ./artisan.
COPY web-app/artisan web-app/webpack.mix.js ./
COPY web-app/resources ./resources
COPY web-app/public ./public
RUN npm run production

# --- Composer packages: the autoloader and scripts run later, on the target PHP ---
FROM composer:${COMPOSER_VERSION} AS vendor
# ARGs are environment variables in RUN, and Composer reads COMPOSER_NO_DEV from the environment.
# Defaults to --no-dev like web-app/.sti/environment; build with COMPOSER_NO_DEV=0 for phpunit/dusk.
ARG COMPOSER_NO_DEV=1
WORKDIR /app
COPY web-app/composer.json web-app/composer.lock ./
RUN composer install --no-interaction --no-progress --no-scripts --no-autoloader

# --- Runtime: Apache + mod_php on port 8080, like the S2I image ---
FROM php:${PHP_VERSION}-apache
# Read by composer dump-autoload below, same as the vendor stage.
ARG COMPOSER_NO_DEV=1
ENV COMPOSER_ALLOW_SUPERUSER=1

COPY --from=vendor /usr/bin/composer /usr/local/bin/composer

RUN a2enmod rewrite headers \
 && echo 'Listen 8080' > /etc/apache2/ports.conf \
 && cp "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"
COPY docker/ecas-frontend/apache-vhost.conf /etc/apache2/sites-available/000-default.conf
COPY docker/ecas-frontend/zz-cve-stopgap.ini $PHP_INI_DIR/conf.d/

WORKDIR /var/www/html
COPY --from=vendor /app/vendor ./vendor
COPY web-app/ ./
COPY --from=assets /app/public ./public

# dump-autoload also runs the post-autoload-dump scripts (artisan package:discover).
RUN composer dump-autoload --optimize --no-interaction \
 && mkdir -p storage/logs storage/framework/cache/data storage/framework/sessions storage/framework/views bootstrap/cache \
 && chown -R www-data:www-data storage bootstrap/cache

USER www-data
EXPOSE 8080
