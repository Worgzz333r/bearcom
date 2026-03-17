FROM php:8.3-fpm

# System dependencies
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    curl \
    libpng-dev \
    libjpeg62-turbo-dev \
    libfreetype6-dev \
    libwebp-dev \
    libzip-dev \
    libicu-dev \
    libxml2-dev \
    libonig-dev \
    default-mysql-client \
    && rm -rf /var/lib/apt/lists/*

# PHP extensions required by Drupal
RUN docker-php-ext-configure gd \
        --with-freetype \
        --with-jpeg \
        --with-webp \
    && docker-php-ext-install -j$(nproc) \
        gd \
        pdo \
        pdo_mysql \
        zip \
        intl \
        opcache \
        mbstring \
        xml \
        bcmath

# Redis extension
RUN pecl install redis && docker-php-ext-enable redis

# Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Drush launcher
RUN curl -fsSL https://github.com/drush-ops/drush-launcher/releases/latest/download/drush.phar -o /usr/local/bin/drush \
    && chmod +x /usr/local/bin/drush

# Set working directory
WORKDIR /var/www

# Create drupal files directory and set permissions
# This fixes permission issues on Linux hosts
RUN mkdir -p /var/www/html/sites/default/files \
    && chown -R www-data:www-data /var/www \
    && chmod -R 755 /var/www

# Use www-data user for file permissions (critical for Linux compatibility)
# On Linux, mounted files keep host UID — ensure www-data can write
RUN usermod -u 1000 www-data && groupmod -g 1000 www-data

EXPOSE 9000
CMD ["php-fpm"]
