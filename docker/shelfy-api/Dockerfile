FROM php:8.2-apache@sha256:934f83240389df24442892b6cba15b71515c0e38abd35182e0029274f036fb6a

ENV DEBIAN_FRONTEND=noninteractive

RUN apt-get update && apt-get install -y --no-install-recommends \
    libzip-dev unzip git curl libonig-dev libxml2-dev \
    && docker-php-ext-install pdo pdo_mysql mysqli zip \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

RUN a2enmod rewrite

WORKDIR /var/www/html

COPY /Backend .

ENV APACHE_DOCUMENT_ROOT=/var/www/html/public

RUN sed -ri "s|DocumentRoot /var/www/html|DocumentRoot ${APACHE_DOCUMENT_ROOT}|g" /etc/apache2/sites-available/000-default.conf \
    && sed -ri "/<Directory \/var\/www\/>/,/<\/Directory>/ s/AllowOverride None/AllowOverride All/" /etc/apache2/apache2.conf

EXPOSE 80
EXPOSE 8080