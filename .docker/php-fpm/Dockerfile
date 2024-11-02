FROM php:8.2-fpm

ENV NGINX_DOCUMENT_ROOT="/var/www/html/public"
ENV COMPOSER_ALLOW_SUPERUSER=1
ENV COMPOSER_PROCESS_TIMEOUT=600

RUN apt-get update && apt-get install -y \
    libfreetype6-dev \
    libjpeg62-turbo-dev \
    libsqlite3-dev \
    libpng-dev \
    libzip-dev \
    libicu-dev \
    libpq-dev \
    nginx \
    unzip \
    gnupg2 \
    default-mysql-client \
    && curl -sL https://deb.nodesource.com/setup_18.x | bash - \
    && apt-get install -y nodejs \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install intl pdo pdo_sqlite pdo_mysql opcache \
    && pecl install redis \
    && docker-php-ext-enable redis \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

#COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/bin --filename=composer

RUN curl -sS https://get.symfony.com/cli/installer | bash
RUN mv /root/.symfony*/bin/symfony /usr/local/bin/symfony && \
    chmod +x /usr/local/bin/symfony

ENV COMPOSER_ORIGINAL_INIS=${PHP_INI_DIR}/conf.d/php-composer.ini
COPY ./.docker/php-fpm/php-composer.ini $COMPOSER_ORIGINAL_INIS
COPY ./.docker/php-fpm/start /usr/local/bin/
RUN chmod +x /usr/local/bin/start

RUN chown -R www-data:www-data /var/www/html && chmod -R 755 /var/www/html

WORKDIR /var/www/html

RUN usermod -u 1000 www-data && usermod -a -G www-data root

CMD ["start"]