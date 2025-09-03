FROM composer/composer:lts-bin AS composer
FROM dunglas/frankenphp:1-php8.4-alpine

COPY --from=composer /composer /usr/bin/composer

RUN apk upgrade --no-cache && \
    apk add --no-cache \
        curl \
        tzdata \
        acl \
        file \
        gettext

RUN install-php-extensions \
		@composer \
    apcu \
    intl \
    opcache

ENV COMPOSER_ALLOW_SUPERUSER=1

RUN cp /usr/local/etc/php/php.ini-production /usr/local/etc/php/php.ini

RUN { \
  echo 'expose_php=0'; \
  echo 'date.timezone=America/Argentina/Buenos_Aires'; \
  echo 'apc.enable_cli=1'; \
  echo 'session.use_strict_mode=1'; \
  echo 'zend.detect_unicode=0'; \
  echo 'realpath_cache_size=4096K'; \
  echo 'realpath_cache_ttl=600'; \
  echo 'opcache.interned_strings_buffer=16'; \
  echo 'opcache.max_accelerated_files=20000'; \
  echo 'opcache.memory_consumption=256'; \
  echo 'opcache.enable_file_override=1'; \
} > /usr/local/etc/php/conf.d/custom.ini

RUN curl -1sLf 'https://dl.cloudsmith.io/public/symfony/stable/setup.alpine.sh' | sh \
 && apk add --no-cache symfony-cli
