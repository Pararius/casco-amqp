FROM php:7.2.10-cli-alpine3.7

RUN apk add --no-cache \
    rabbitmq-c-dev \
    zlib-dev

RUN apk add --virtual .build-dependencies --no-cache \
    autoconf \
    curl \
    gcc \
    git \
    libc-dev \
    make \
    && pecl channel-update pecl.php.net \
    && pecl install amqp \
    && docker-php-ext-enable amqp \
    && apk del .build-dependencies

RUN docker-php-ext-install \
    zip

RUN curl -sS -o /tmp/composer-setup.php https://getcomposer.org/installer \
    && curl -sS -o /tmp/composer-setup.sig https://composer.github.io/installer.sig \
    && php -r "if (hash('SHA384', file_get_contents('/tmp/composer-setup.php')) !== trim(file_get_contents('/tmp/composer-setup.sig'))) { unlink('/tmp/composer-setup.php'); echo 'Invalid installer' . PHP_EOL; exit(1); }" \
    && php /tmp/composer-setup.php --install-dir=/usr/bin --filename=composer --snapshot \
    && rm -rf /tmp/composer-setup.php \
    && composer self-update

WORKDIR /app
