FROM php:8.0-fpm

# 必要なパッケージをインストール
RUN apt-get update && apt-get install -y \
    libzip-dev zip unzip \
    && docker-php-ext-install pdo_mysql zip

# Composerをインストール
COPY --from=composer:2.1 /usr/bin/composer /usr/bin/composer

# 作業ディレクトリを設定
WORKDIR /var/www

# ソースコードをコンテナにコピー
COPY . /var/www

# 必要なディレクトリを作成して権限を設定
RUN mkdir -p /var/www/storage /var/www/bootstrap/cache \
    && chown -R www-data:www-data /var/www \
    && chmod -R 775 /var/www/storage /var/www/bootstrap/cache
