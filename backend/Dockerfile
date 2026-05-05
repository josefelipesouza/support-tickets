FROM php:8.3-fpm

RUN apt-get update && apt-get install -y \
    git curl libpng-dev libonig-dev libxml2-dev libzip-dev \
    librabbitmq-dev zip unzip \
    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

RUN pecl install amqp && docker-php-ext-enable amqp

COPY --from=composer:2.7 /usr/bin/composer /usr/bin/composer

RUN printf '[www]\nlisten = 0.0.0.0:9000\n' > /usr/local/etc/php-fpm.d/zz-docker.conf

WORKDIR /var/www

EXPOSE 9000
CMD ["php-fpm"]
