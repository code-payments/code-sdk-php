FROM php:8.0-cli

ENV COMPOSER_ALLOW_SUPERUSER=1

RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libzip-dev \
&& docker-php-ext-install zip \
&& rm -rf /var/lib/apt/lists/*

WORKDIR /app

# Install Composer, PHP's package manager
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Install GMP extension
RUN apt-get update && apt-get install -y libgmp-dev && docker-php-ext-install gmp

# Copy composer.json (which we will create next) and install dependencies
COPY composer.json ./
RUN composer install --no-interaction

# Copy source code and tests into the container
COPY src/ /app/src/
COPY tests/ /app/tests/
COPY example/ /app/example/
