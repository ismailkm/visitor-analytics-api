FROM php:8.2-fpm-alpine

WORKDIR /var/www

RUN apk add --no-cache \
    build-base \
    libzip-dev \
    libpng-dev \
    jpeg-dev \
    mysql-client \
    oniguruma-dev \
    freetype-dev \
    libjpeg-turbo-dev \
    gmp-dev \
    icu-dev \
    autoconf \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) gd pdo_mysql zip exif pcntl bcmath gmp intl opcache \
    && pecl install redis \
    && docker-php-ext-enable redis \
    && rm -rf /tmp/*

# Install Composer globally in the container
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Copy the application code into the container
COPY . .

# Run composer install to get PHP dependencies.
RUN composer install --no-interaction --no-dev --optimize-autoloader

EXPOSE 9000
