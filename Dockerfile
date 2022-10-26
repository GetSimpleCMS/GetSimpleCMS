FROM php:8-apache
RUN apt-get update && apt upgrade -y && \
apt-get install -y libzip-dev zip libpng-dev && \
docker-php-ext-install gd zip && \
docker-php-ext-enable gd zip && \
a2enmod rewrite && \
service apache2 restart
ADD ./ /var/www/html
RUN chmod -R 777 data
RUN chmod -R 777 backups
EXPOSE 8080
