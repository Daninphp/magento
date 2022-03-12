FROM php:7.3-fpm

# Set Environment Variables
ENV DEBIAN_FRONTEND noninteractive

#RUN apt-get update &&\
#    apt-get install --no-install-recommends --assume-yes --quiet ca-certificates curl git &&\
#    rm -rf /var/lib/apt/lists/*
#RUN curl -Lsf 'https://storage.googleapis.com/golang/go1.8.3.linux-amd64.tar.gz' | tar -C '/usr/local' -xvzf -
#ENV PATH /usr/local/go/bin:$PATH
#RUN go get github.com/mailhog/mhsendmail
#RUN cp /root/go/bin/mhsendmail /usr/bin/mhsendmail
#RUN echo 'sendmail_path = /usr/bin/mhsendmail --smtp-addr mailhog:1025' > /usr/local/etc/php/php.ini

RUN set -eux; \
    apt-get update; \
    apt-get upgrade -y; \
    apt-get install -y --no-install-recommends \
            curl \
            libmemcached-dev \
            libz-dev \
            libpq-dev \
            libjpeg-dev \
            libpng-dev \
            libfreetype6-dev \
            libssl-dev \
            libmcrypt-dev \
            libbz2-dev \
            libxml2-dev \
            libicu-dev \
            libzip-dev \
            libonig-dev; \
    rm -rf /var/lib/apt/lists/*

RUN set -eux; \
    docker-php-ext-install bcmath; \
    docker-php-ext-install bz2; \
    docker-php-ext-install calendar; \
    docker-php-ext-install iconv; \
    docker-php-ext-install intl; \
    docker-php-ext-install mbstring; \
    docker-php-ext-install mysqli; \
    docker-php-ext-install opcache; \
    docker-php-ext-install pdo_mysql; \
    docker-php-ext-install pdo_pgsql; \
    docker-php-ext-install pgsql; \
    docker-php-ext-install soap; \
    docker-php-ext-install zip; \
    docker-php-ext-configure gd \
            --prefix=/usr \
            --with-png-dir \
            --with-jpeg-dir \
            --with-freetype-dir; \
    docker-php-ext-install gd; \
    php -r 'var_dump(gd_info());'

RUN addgroup --group rewe && adduser --ingroup rewe --no-create-home --shell /bin/bash rewe
