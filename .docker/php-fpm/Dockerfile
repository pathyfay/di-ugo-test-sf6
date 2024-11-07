FROM php:8.3-fpm

ENV COMPOSER_ALLOW_SUPERUSER=1 \
    COMPOSER_PROCESS_TIMEOUT=600 \
    NGINX_DOCUMENT_ROOT="/var/www/html/public"

RUN set -eux; \
    apt-get update && apt-get install -y  --no-install-recommends \
    ca-certificates \
    curl \
    gnupg2 \
    bash \
    nginx \
    supervisor \
    cron \
    zip \
    git \
    unzip \
    vim \
    netcat-openbsd \
    procps \
    tesseract-ocr poppler-utils imagemagick \
    libfreetype6-dev \
    libjpeg62-turbo-dev \
    libsqlite3-dev \
    libpng-dev \
    libzip-dev \
    libicu-dev \
    libpq-dev \
    default-mysql-client \
    ; \
    rm -rf /var/lib/apt/lists/*

RUN set -eux; \
    curl -fsSL https://deb.nodesource.com/gpgkey/nodesource-repo.gpg.key \
      | gpg --dearmor -o /usr/share/keyrings/nodesource.gpg; \
    echo "deb [signed-by=/usr/share/keyrings/nodesource.gpg] https://deb.nodesource.com/node_18.x nodistro main" \
      > /etc/apt/sources.list.d/nodesource.list; \
    apt-get update; \
    apt-get install -y --no-install-recommends nodejs npm; \
    corepack enable; \
    corepack prepare yarn@stable --activate; \
    rm -rf /var/lib/apt/lists/*

RUN set -eux; \
    docker-php-ext-configure gd --with-freetype --with-jpeg; \
    docker-php-ext-install -j"$(nproc)" intl pdo pdo_mysql pdo_sqlite pdo_pgsql opcache gd; \
    pecl install redis; \
    docker-php-ext-enable redis

#COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/bin --filename=composer

RUN curl -sS https://get.symfony.com/cli/installer | bash
RUN mv /root/.symfony*/bin/symfony /usr/local/bin/symfony && \
    chmod +x /usr/local/bin/symfony

# Assurez-vous que le répertoire de sessions est accessible
RUN mkdir -p /tmp/sessions && chown -R www-data:www-data /tmp/sessions

ENV COMPOSER_ORIGINAL_INIS=${PHP_INI_DIR}/conf.d/php-composer.ini
COPY ./.docker/php-fpm/php-composer.ini $COMPOSER_ORIGINAL_INIS
COPY ./.docker/php-fpm/supervisord.conf /etc/supervisor/conf.d/supervisord.conf
COPY ./.docker/php-fpm/ftk-it-cron /etc/cron.d/ftk-it-cron
COPY ./.docker/php-fpm/start /usr/local/bin/
RUN chmod +x /usr/local/bin/start

RUN chown -R www-data:www-data /var/www/html && chmod -R 755 /var/www/html

WORKDIR /var/www/html

RUN usermod -u 1000 www-data && usermod -a -G www-data root

CMD ["/usr/local/bin/start"]