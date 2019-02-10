FROM php:7-cli

RUN apt-get update && \
    apt-get install -y \
        libzip-dev \
        unzip && \
    docker-php-ext-install \
        zip
