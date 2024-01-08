FROM php:8.2-fpm-alpine

# update dependencies
RUN apk upgrade --no-cache --update

# install dependencies
 RUN apk --no-cache add icu-dev

# install composer
ADD https://getcomposer.org/download/latest-stable/composer.phar /usr/local/bin/composer
RUN chmod +x /usr/local/bin/composer

# install our packages (feel free to strip these down as required)
# RUN install-php-extensions pcov pdo_mysql imagick gd exif zip
RUN docker-php-ext-configure intl
RUN docker-php-ext-install pdo_mysql opcache intl

# set our work directory
WORKDIR /var/www
