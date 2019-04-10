FROM php:7.2.11-cli-alpine3.7

RUN docker-php-ext-install \
    bcmath \
    sockets \
    pcntl

RUN curl -sS -o /tmp/composer-setup.php https://getcomposer.org/installer \
    && curl -sS -o /tmp/composer-setup.sig https://composer.github.io/installer.sig \
    && php -r "if (hash('SHA384', file_get_contents('/tmp/composer-setup.php')) !== trim(file_get_contents('/tmp/composer-setup.sig'))) { unlink('/tmp/composer-setup.php'); echo 'Invalid installer' . PHP_EOL; exit(1); }" \
    && php /tmp/composer-setup.php --install-dir=/usr/bin --filename=composer --snapshot \
    && rm -rf /tmp/composer-setup.php \
    && composer self-update

WORKDIR /app
