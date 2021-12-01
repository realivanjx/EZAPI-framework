FROM php:8.0-apache

# Install MySQLi, PDO & PDO_MYSQL
RUN docker-php-ext-install mysqli pdo pdo_mysql

#Update packages
RUN apt-get update && apt-get upgrade -y

#Enable rewrite
RUN a2enmod rewrite

#Restart service
RUN service apache2 restart