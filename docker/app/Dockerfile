FROM php:8.2-fpm-alpine

ARG user
ARG uid

# Add packages. Shadow need to install useradd
RUN apk update && apk add \
    curl \
    libpng-dev \
    libxml2-dev \
    zip \
    unzip \
    shadow

RUN docker-php-ext-install pdo pdo_mysql \
    && apk --no-cache add nodejs npm

# Configure PHP-FPM docker/app/php.ini
COPY ./app/php.ini /usr/local/etc/php/php.ini

# install composer
COPY --from=composer:latest /usr/bin/composer /usr/local/bin/composer

RUN useradd -G www-data,root -u $uid -d /home/$user $user

RUN mkdir -p /home/$user/.composer && \
    chown -R $user:$user /home/$user

WORKDIR /var/www

USER $user
