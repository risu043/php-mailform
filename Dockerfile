FROM php:8.2-apache

# 必要なモジュールをインストール
RUN docker-php-ext-install mysqli pdo pdo_mysql && \
    apt-get update && apt-get install -y libpng-dev libjpeg-dev libfreetype6-dev && \
    docker-php-ext-configure gd --with-freetype --with-jpeg && \
    docker-php-ext-install gd

# Apacheのmod_rewriteを有効化
RUN a2enmod rewrite

# 作業ディレクトリの設定
WORKDIR /var/www/html

# ローカルのファイルをコンテナにコピー
COPY ./app /var/www/html

# パーミッションの設定
RUN chown -R www-data:www-data /var/www/html && chmod -R 755 /var/www/html

# コンテナ起動時にApacheを実行
CMD ["apache2-foreground"]
