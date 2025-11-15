FROM php:8.3-fpm

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libicu-dev \
    libpq-dev \
    libzip-dev \
    libonig-dev \
    && docker-php-ext-install intl pdo pdo_mysql zip opcache \
    && rm -rf /var/lib/apt/lists/*

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www/html

# Copy existing application
COPY . .

# Install PHP dependencies
RUN composer install --no-interaction --no-progress --no-suggest

EXPOSE 9000
