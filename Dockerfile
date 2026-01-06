FROM dunglas/frankenphp:php8.4

ENV SERVER_NAME=':80'
ENV FRANKENPHP_CONFIG='worker /app/public/index.php'

ENV APP_ENV='prod'
ENV APP_DEBUG='0'

RUN apt-get update \
    && apt-get install -y --no-install-recommends git unzip \
    && rm -rf /var/lib/apt/lists/*

RUN install-php-extensions apcu intl zip pdo_pgsql @composer

RUN mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"
COPY <<-EOF /usr/local/etc/php/conf.d/local.ini
	date.timezone = "Europe/Berlin"
	short_open_tag = Off
	expose_php = Off
	opcache.preload_user = root
	opcache.preload = /app/config/preload.php
EOF

COPY . /app
RUN composer install --no-dev --optimize-autoloader -d /app/
