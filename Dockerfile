FROM php:8.0-apache
RUN apt-get update && apt upgrade -y && \
apt-get install -y libzip-dev zip libpng-dev libicu-dev nano && \
docker-php-ext-install gd zip intl && \
docker-php-ext-enable gd zip intl && \
a2enmod rewrite && \
service apache2 restart

ADD ./ /var/www/html
COPY ./ /var/www/html
COPY composer.json /var/www/html
RUN chmod -R 777 data
RUN chmod -R 777 backups
RUN chmod -R 777 theme
EXPOSE 8080

COPY --from=composer/composer:latest-bin /composer /usr/local/bin/composer
# COPY --from=composer/composer /usr/bin/composer /usr/bin/composer
RUN composer require rector/rector --dev

# docker build --no-cache . -t gs_rector

# vendor/bin/rector list-rules --output-format json > rector_rules_out.json
# vendor/bin/rector process --dry-run plugins

