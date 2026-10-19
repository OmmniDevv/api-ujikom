FROM php:8.4-cli

RUN apt-get update -q \
    && apt-get install -y -q libzip-dev libpng-dev libonig-dev libxml2-dev zip unzip curl git \
    && docker-php-ext-install pdo pdo_mysql zip mbstring \
    && curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer \
    && curl -fsSL https://deb.nodesource.com/setup_20.x | bash - \
    && apt-get install -y -q nodejs \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

WORKDIR /var/www

CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8000", "--no-reload"]
