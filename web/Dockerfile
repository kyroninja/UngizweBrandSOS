FROM php:8.2-apache

RUN apt-get update && apt-get install -y \
        libssl-dev \
        libzip-dev \
        zip \
        unzip \
    && docker-php-ext-install \
        mysqli \
        pdo \
        pdo_mysql \
        zip \
    && pecl install redis \
    && docker-php-ext-enable redis \
    && a2enmod rewrite \
    && echo '<FilesMatch "\.html$">' >> /etc/apache2/conf-available/php-in-html.conf \
    && echo '    SetHandler application/x-httpd-php' >> /etc/apache2/conf-available/php-in-html.conf \
    && echo '</FilesMatch>' >> /etc/apache2/conf-available/php-in-html.conf \
    && a2enconf php-in-html \
    && rm -rf /var/lib/apt/lists/*

WORKDIR /var/www/html
