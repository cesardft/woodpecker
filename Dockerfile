FROM ubuntu:18.04

RUN apt-get update
RUN apt-get install -y wget curl nano htop git unzip bzip2 software-properties-common locales

WORKDIR /var/www/html

RUN LC_ALL=C.UTF-8 add-apt-repository ppa:ondrej/php
RUN apt update
RUN apt-get install -y \
    php7.4-fpm \
    php7.4-common \
    php7.4-curl \
    php7.4-mysql \
    php7.4-mbstring \
    php7.4-json \
    php7.4-xml \
    php7.4-bcmath

ADD src/resources/www.conf /etc/php/7.4/fpm/pool.d/www.conf
RUN mkdir -p /var/run/php

RUN apt-key adv --keyserver keyserver.ubuntu.com --recv-keys ABF5BD827BD9BF62
RUN apt-key adv --keyserver keyserver.ubuntu.com --recv-keys 4F4EA0AAE5267A6C
RUN echo "deb http://nginx.org/packages/ubuntu/ trusty nginx" >> /etc/apt/sources.list
RUN echo "deb-src http://nginx.org/packages/ubuntu/ trusty nginx" >> /etc/apt/sources.list
RUN apt-get update

RUN apt-get install -y nginx

ADD src/resources/default /etc/nginx/sites-enabled/
ADD src/resources/nginx.conf /etc/nginx/

RUN chown -R www-data:www-data .

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

EXPOSE 80

ENTRYPOINT ["/usr/bin/supervisord"]

