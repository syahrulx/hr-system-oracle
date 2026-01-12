# Use pre-built PHP with OCI8 image
FROM ghcr.io/mlocati/php-extension-installer:latest AS ext-installer
FROM php:8.2-fpm

# Copy extension installer
COPY --from=ext-installer /usr/bin/install-php-extensions /usr/local/bin/

# Install OCI8 and other extensions
RUN install-php-extensions oci8 pdo_oci gd mbstring bcmath pcntl zip

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

EXPOSE 9000
CMD ["php-fpm"]
