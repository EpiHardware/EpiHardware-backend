FROM php:8.2-cli

RUN apt-get update \
    &&  apt-get install -y --no-install-recommends --fix-missing\
        locales apt-utils git libicu-dev g++ libpng-dev libxml2-dev libzip-dev libonig-dev libxslt-dev unzip libpq-dev nodejs npm wget \
        apt-transport-https lsb-release ca-certificates entr

RUN echo "en_US.UTF-8 UTF-8" > /etc/locale.gen  \
    &&  echo "fr_FR.UTF-8 UTF-8" >> /etc/locale.gen \
    &&  locale-gen

RUN curl -sS https://getcomposer.org/installer | php -- \
    &&  mv composer.phar /usr/local/bin/composer

RUN curl -sS https://get.symfony.com/cli/installer | bash \
    &&  mv /root/.symfony5/bin/symfony /usr/local/bin

RUN docker-php-ext-configure \
            intl \
    &&  docker-php-ext-install \
            pdo pdo_mysql pdo_pgsql opcache intl zip calendar dom mbstring gd xsl

RUN pecl install apcu && docker-php-ext-enable apcu

RUN npm install --global yarn

RUN git config --global user.email "you@example.com" \
    &&  git config --global user.name "Your Name"


WORKDIR /var/www/html/
COPY . .
ENV COMPOSER_ALLOW_SUPERUSER=1

CMD tail -f /dev/null

WORKDIR /var/www/html/
RUN composer install


CMD symfony server:start