FROM php:8-apache
RUN apt-get update && apt upgrade -y
ADD ./ /var/www/html
RUN chmod -R 777 data
RUN chmod -R 777 backups
EXPOSE 8080