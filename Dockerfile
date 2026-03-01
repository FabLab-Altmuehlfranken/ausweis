FROM dunglas/frankenphp:php8.5

ENV SERVER_NAME=':80'
ENV FRANKENPHP_CONFIG='worker /app/public/index.php'

ENV APP_ENV='prod'
ENV APP_DEBUG='0'

RUN apt-get update \
    && apt-get install -y --no-install-recommends git unzip acl \
    && rm -rf /var/lib/apt/lists/*

RUN install-php-extensions apcu gd intl zip pdo_pgsql @composer

RUN mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"
COPY <<-EOF /usr/local/etc/php/conf.d/local.ini
	date.timezone = "Europe/Berlin"
	short_open_tag = Off
	expose_php = Off
	opcache.preload_user = root
	opcache.preload = /app/config/preload.php
EOF

RUN useradd ausweis \
	&& setcap -r /usr/local/bin/frankenphp \
	&& chown -R ausweis:ausweis /config/caddy /data/caddy /app
USER ausweis

COPY --chown=ausweis:ausweis . /app
RUN composer install --no-dev --optimize-autoloader -d /app/ \
    && /app/bin/console asset-map:compile

ADD --chmod=755 https://github.com/api-platform/api-platform/raw/refs/heads/main/api/frankenphp/docker-entrypoint.sh /usr/local/bin/custom-entrypoint
ENTRYPOINT ["/usr/local/bin/custom-entrypoint"]
CMD [ "frankenphp", "run", "--config", "/etc/frankenphp/Caddyfile" ]
