FROM php:7.1-cli

RUN apt-get update && apt-get install -y \
        automake \
        autotools-dev \
        curl \
        git \
        libmcrypt-dev \
        make \
        default-mysql-client \
        sqlite3 \
        wget \
        zip \
        zlib1g-dev \
    && apt-get clean

# Install php libraries
RUN docker-php-ext-enable opcache \
    && docker-php-ext-install \
        bcmath \
        mcrypt \
        mysqli \
        pcntl \
        pdo \
        pdo_mysql \
        zip

# Install xdebug
RUN pecl install xdebug \
    && docker-php-ext-enable xdebug \
    && sed -i '1 a xdebug.remote_autostart=true' /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
    && sed -i '1 a xdebug.idekey=phpstorm' /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
    && sed -i '1 a xdebug.remote_mode=req' /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
    && sed -i '1 a xdebug.remote_handler=dbgp' /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
    && sed -i '1 a xdebug.remote_connect_back=0 ' /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
    && sed -i '1 a xdebug.remote_port=9000' /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
    && sed -i '1 a xdebug.remote_host=10.254.254.254' /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
    && sed -i '1 a xdebug.remote_enable=1' /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini

# Setup php
RUN echo "date.timezone=UTC"                >  /usr/local/etc/php/conf.d/timezone.ini \
    && echo "memory_limit=-1"               >  /usr/local/etc/php/conf.d/memory.ini \
    && echo "phar.readonly = false"         > /usr/local/etc/php/conf.d/phar.ini

# Install composer
RUN wget https://raw.githubusercontent.com/composer/getcomposer.org/5eb0614d3fa7130b363698d3dca52c619b463615/web/installer -O - -q | php -- && cp composer.phar /usr/local/bin/composer

WORKDIR /var/www/html
VOLUME /var/www/html
