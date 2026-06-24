# Sử dụng đúng phiên bản PHP 8.3 theo máy local của bạn
FROM php:8.3-apache

# Cài đặt các thư viện lõi cần thiết cho Laravel
RUN apt-get update -y && apt-get install -y openssl zip unzip git libonig-dev
RUN docker-php-ext-install pdo pdo_mysql mbstring

# Cài đặt Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Copy toàn bộ code vào trong server
COPY . /var/www/html
WORKDIR /var/www/html

# Chạy cài đặt các package Laravel
RUN composer install --no-dev --optimize-autoloader

# Phân quyền cho thư mục lưu trữ để không bị lỗi 500
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

# Cấu hình Apache trỏ thẳng vào thư mục public của Laravel
ENV APACHE_DOCUMENT_ROOT /var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf
RUN a2enmod rewrite

# Lệnh khởi chạy: Tự động đồng bộ DB online và bật server web
CMD php artisan migrate --force && apache2-foreground