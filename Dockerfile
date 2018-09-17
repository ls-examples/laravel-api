FROM php:7.1-fpm

ENV COMPOSER_ALLOW_SUPERUSER 1

RUN apt-get update && apt-get install -y --force-yes \
        libfreetype6-dev \
        libmcrypt-dev \
        libmysqlclient-dev  \
        libpng12-dev \
        libjpeg-dev \
        libpcre3-dev \
        git \
        vim \
        openssh-client \
        openssl \
        curl \
        nano \
        libcurl3-dev \
        zlib1g-dev \
        libicu-dev g++ \

   && docker-php-ext-install -j$(nproc) mysqli \
   && docker-php-ext-install -j$(nproc) mysql; \
      docker-php-ext-install -j$(nproc) pdo; \
      docker-php-ext-install -j$(nproc) pdo_mysql \
   && docker-php-ext-install -j$(nproc) iconv mcrypt \
   && docker-php-ext-configure gd --with-freetype-dir=/usr/include/ --with-jpeg-dir=/usr/include/ \
   && docker-php-ext-install -j$(nproc) gd \
   && docker-php-ext-install -j$(nproc) mbstring \
   && docker-php-ext-install -j$(nproc) curl \
   && docker-php-ext-install -j$(nproc) zip \
   && docker-php-ext-configure intl \
   && docker-php-ext-install intl \
   && pecl install timezonedb \
   && apt-get install -my wget gnupg \
   && curl -sL https://deb.nodesource.com/setup_8.x | bash - \
   && apt-get install -y nodejs build-essential openssh-server \
   && apt-get install -y fuse sshfs \
   && docker-php-ext-install -j$(nproc) opcache \
   && apt-get install -y nginx mysql-client \
   && apt-get -q clean

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

RUN ulimit -s unlimited || true

RUN sed -ri -e 's!9000!9010!g' /usr/local/etc/php-fpm.d/www.conf
RUN sed -ri -e 's!9000!9010!g' /usr/local/etc/php-fpm.d/zz-docker.conf
COPY build/config/nginx_default.conf /etc/nginx/sites-available/default
COPY build/config/nginx.conf /etc/nginx/nginx.conf
RUN apt-get update && apt-get install -y locales && localedef -i en_US -f UTF-8 en_US.UTF-8 && apt-get clean
#RUN locale-gen en_US.UTF-8 && apt-get clean
ENV LANG en_US.UTF-8
ENV LANGUAGE en_US:en
ENV LC_ALL en_US.UTF-8

EXPOSE 80

WORKDIR /var/www/html

COPY composer.json ./
COPY composer.lock ./
RUN composer update --no-scripts --no-autoloader

ADD build/entrypoint.sh /bin/
RUN chmod +x /bin/entrypoint.sh

COPY --chown=33:33 ./ /var/www/html
ENV APP_ENV=dev
RUN composer dump-autoload --optimize

RUN mkdir /var/run/sshd && \
    usermod -s /bin/bash www-data && \
    chown www-data:www-data /var/www && \
    ulimit -s unlimited || true



RUN apt-get update && apt-get install -y locales && localedef -i ru_RU -f UTF-8 ru_RU.UTF-8 && apt-get clean
ENV LANG ru_RU.UTF-8
ENV LANGUAGE ru_RU:ru
ENV LC_ALL ru_RU.utf-8
COPY build/config/php.ini /usr/local/etc/php/conf.d/php.ini

ENTRYPOINT "/bin/entrypoint.sh"
