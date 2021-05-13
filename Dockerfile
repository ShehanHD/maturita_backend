FROM php:7.4-apache

COPY . /var/www/html/
RUN apt update && apt upgrade -y 

RUN docker-php-ext-install mysqli pdo pdo_mysql
RUN a2enmod rewrite && service apache2 restart

# EXPOSE 80
