FROM docker.io/library/php:8.5-apache
COPY . /var/www/html/
RUN docker-php-ext-install mysqli pdo_mysql pdo && docker-php-ext-enable mysqli
RUN chown -R www-data:www-data /var/www/html && chmod -R 755 /var/www/html
EXPOSE 80
ENV DB_USER=""
ENV DB_NAME=""
ENV DB_HOST=""
