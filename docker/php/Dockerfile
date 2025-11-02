
FROM php:8.4-fpm

RUN apt-get update && apt-get install -y \
    git unzip curl zip libpq-dev libzip-dev libonig-dev libpng-dev \
    && docker-php-ext-install pdo pdo_pgsql mbstring zip bcmath exif pcntl \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/bin --filename=composer
ENV COMPOSER_ALLOW_SUPERUSER=1

WORKDIR /var/www/html

COPY composer.json composer.lock ./

RUN composer install --no-dev --optimize-autoloader --no-interaction --prefer-dist || true

COPY . .

EXPOSE 3010

CMD ["php-fpm"]
