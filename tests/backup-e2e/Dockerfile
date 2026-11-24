# PHP 8.1 (the lowest version composer.lock installs on: symfony/* 6.2
# needs >= 8.1, nette/utils 4.0 < 8.3) with php-fpm + nginx, like a VPS.
# Alpine's mysqldump is MariaDB's, run against a MySQL 8.0 server: the
# client/server mix a Debian/Ubuntu-family server would most likely have.
FROM php:8.1.34-fpm-alpine
RUN apk add --no-cache bash coreutils git nginx mariadb-client mariadb-connector-c shellcheck curl openssl tar gzip findutils diffutils \
        libpng libzip icu-libs \
    && apk add --no-cache --virtual .build $PHPIZE_DEPS libpng-dev libzip-dev icu-dev linux-headers \
    && docker-php-ext-install -j4 pdo_mysql gd zip bcmath intl pcntl \
    && apk del .build
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer
RUN git config --global user.email t@t && git config --global user.name t \
    && git config --global init.defaultBranch main \
    && echo 'memory_limit=256M' > /usr/local/etc/php/conf.d/zz-test.ini
WORKDIR /app
