FROM php:8.3-fpm-alpine

# Install system dependencies & PHP extensions
RUN apk add --no-cache nginx supervisor curl libpng-dev libxml2-dev zip unzip git \
    && docker-php-ext-install pdo_mysql bcmath gd

# Get latest Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www

# Copy project files
COPY . .

# Install Laravel dependencies
RUN composer install --no-dev --optimize-autoloader

# Setup permissions
RUN chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache

# Setup Nginx configuration inline
RUN echo 'server { \
    listen 80; \
    root /var/www/public; \
    index index.php index.html; \
    location / { try_files $uri $uri/ /index.php?$query_string; } \
    location ~ \.php$ { include fastcgi_params; fastcgi_pass 127.0.0.1:9000; fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name; } \
}' > /etc/nginx/http.d/default.conf

# Expose port
EXPOSE 80

# Start Nginx & PHP-FPM using a simple command
CMD php-fpm -D && nginx -g "daemon off;"