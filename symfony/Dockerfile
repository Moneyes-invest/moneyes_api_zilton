#syntax=docker/dockerfile:1.4
ARG PHP_VERSION=8.1
ARG CADDY_VERSION=2

# "php" stage
FROM php:${PHP_VERSION}-fpm-alpine as php

# persistent / runtime deps
RUN apk add --no-cache \
		acl \
		fcgi \
		file \
		gettext \
		git \
		gnu-libiconv \
		nodejs \
		npm \
	;

# install gnu-libiconv and set LD_PRELOAD env to make iconv work fully on Alpine image.
# see https://github.com/docker-library/php/issues/240#issuecomment-763112749
ENV LD_PRELOAD /usr/lib/preloadable_libiconv.so

RUN set -eux; \
	apk update; \
	apk upgrade -U -a --purge; \
	apk add --no-cache --virtual .build-deps \
		$PHPIZE_DEPS \
		icu-data-full \
		icu-dev \
		libzip-dev \
		zlib-dev \
		postgresql-dev \
        libxml2-dev \
        rabbitmq-c rabbitmq-c-dev \
	; \
	docker-php-ext-configure zip; \
	docker-php-ext-install -j$(nproc) \
		intl \
		pdo_pgsql \
		zip \
	; \
	pecl install \
		apcu \
        amqp \
	; \
	pecl clear-cache; \
	docker-php-ext-enable \
		apcu \
		opcache \
        amqp \
	; \
	\
	runDeps="$( \
		scanelf --needed --nobanner --format '%n#p' --recursive /usr/local/lib/php/extensions \
			| tr ',' '\n' \
			| sort -u \
			| awk 'system("[ -e /usr/local/lib/" $1 " ]") == 0 { next } { print "so:" $1 }' \
	)"; \
	apk add --no-cache --virtual .app-phpexts-rundeps $runDeps; \
	\
	apk del .build-deps


RUN mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"
COPY --link docker/php/conf.d/app.ini $PHP_INI_DIR/conf.d/
COPY --link ./docker/php/php-fpm.d/zz-docker.conf /usr/local/etc/php-fpm.d/zz-docker.conf

COPY --link ./docker/php/docker-healthcheck.sh /usr/local/bin/docker-healthcheck
RUN chmod +x /usr/local/bin/docker-healthcheck

HEALTHCHECK --interval=10s --timeout=3s --retries=3 CMD ["docker-healthcheck"]

COPY --link ./docker/php/docker-entrypoint.sh /usr/local/bin/docker-entrypoint
RUN chmod +x /usr/local/bin/docker-entrypoint

# build for production
ARG APP_ENV=prod 

WORKDIR /srv/app

# https://getcomposer.org/doc/03-cli.md#composer-allow-superuser
ENV COMPOSER_ALLOW_SUPERUSER=1
ENV PATH="${PATH}:/root/.composer/vendor/bin"

COPY --from=composer:2 --link /usr/bin/composer /usr/bin/composer

# prevent the reinstallation of vendors at every changes in the source code
COPY composer.* symfony.* ./
RUN set -eux; \
	composer install --prefer-dist --no-dev --no-autoloader --no-scripts --no-progress; \
	composer clear-cache

# copy sources
COPY --link . .
RUN rm -rf docker/

RUN set -eux; \
	mkdir -p var/cache var/log; \
	composer dump-autoload --classmap-authoritative --no-dev; \
	#composer run-script --no-dev post-install-cmd; \
	chmod +x bin/console; sync

ENTRYPOINT ["docker-entrypoint"]
CMD ["php-fpm"]

FROM caddy:${CADDY_VERSION} as caddy

COPY --link docker/caddy/maintenance.html /etc/caddy/maintenance.html
COPY --link docker/caddy/Caddyfile /etc/caddy/Caddyfile

WORKDIR /srv/app
COPY --from=php --link /srv/app/public public/

ENV MAINTENANCE_MODE 0

FROM php as php_dev

ENV APP_ENV=dev XDEBUG_MODE=off
VOLUME /srv/app/var

RUN mv "$PHP_INI_DIR/php.ini" "$PHP_INI_DIR/php.ini-production"; \
	mv "$PHP_INI_DIR/php.ini-development" "$PHP_INI_DIR/php.ini"

RUN set -eux; \
 apk add --no-cache --virtual .build-deps $PHPIZE_DEPS linux-headers; \
 pecl install xdebug; \
 docker-php-ext-enable xdebug; \
 apk del .build-deps;
