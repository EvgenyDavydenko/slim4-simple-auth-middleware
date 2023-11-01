FROM php:7.4.33-cli-bullseye
RUN apt update
RUN apt install -y git zip curl
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
WORKDIR /usr/src/application

RUN usermod -u 1000 www-data && groupmod -g 1000 www-data
USER "1000:1000"