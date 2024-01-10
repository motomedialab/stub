FROM php:8.3-fpm-alpine

# update dependencies
RUN apk upgrade --no-cache --update

# install dependencies
 RUN apk --no-cache add icu-dev

# install composer
ADD https://getcomposer.org/download/latest-stable/composer.phar /usr/local/bin/composer
RUN chmod +x /usr/local/bin/composer

# install php extensions
ADD https://github.com/mlocati/docker-php-extension-installer/releases/latest/download/install-php-extensions /usr/local/bin/
RUN chmod +x /usr/local/bin/install-php-extensions

# install our packages (feel free to strip these down as required)
RUN install-php-extensions pcov pdo_mysql opcache intl

# set our work directory
WORKDIR /var/www
