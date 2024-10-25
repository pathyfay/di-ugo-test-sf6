FROM php:8.2-fpm

ENV NGINX_DOCUMENT_ROOT="/var/www/html/public"
ENV COMPOSER_ALLOW_SUPERUSER=1
ENV COMPOSER_PROCESS_TIMEOUT=600

RUN apt-get update && apt-get install -y \
    libfreetype6-dev \
    libjpeg62-turbo-dev \
    libpng-dev \
    libzip-dev \
    unzip \
    gnupg2 \
    && curl -sL https://deb.nodesource.com/setup_18.x | bash - \
    && apt-get install -y nodejs \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

RUN curl -sS https://get.symfony.com/cli/installer | bash
RUN mv /root/.symfony*/bin/symfony /usr/local/bin/symfony && \
    chmod +x /usr/local/bin/symfony

ENV COMPOSER_ORIGINAL_INIS=${PHP_INI_DIR}/conf.d/php-composer.ini
COPY ./.docker/php-fpm8/php-composer.ini $COMPOSER_ORIGINAL_INIS
COPY ./.docker/start /usr/local/bin/

RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html

WORKDIR /var/www/html

RUN usermod -u 1000 www-data \
    && usermod -a -G www-data root \
    && chmod +x /usr/local/bin/start

CMD ["start"]