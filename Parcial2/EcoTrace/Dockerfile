FROM php:8.4-cli
RUN apt-get update && apt-get install -y --no-install-recommends \
    libpq-dev libicu-dev libzip-dev libonig-dev libcurl4-openssl-dev libsqlite3-dev git unzip \
    && docker-php-ext-install pdo_pgsql pdo_sqlite intl zip pcntl bcmath mbstring curl \
    && rm -rf /var/lib/apt/lists/*
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer
ENV COMPOSER_ALLOW_SUPERUSER=1
WORKDIR /var/www/html
