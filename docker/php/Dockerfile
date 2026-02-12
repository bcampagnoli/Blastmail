FROM php:8.3-cli

# Instala dependências do sistema necessárias
RUN apt-get update && apt-get install -y \
    unzip \
    git \
    zip \
    libzip-dev \
    && rm -rf /var/lib/apt/lists/*

# Instala extensões PHP comuns
RUN docker-php-ext-install pdo pdo_mysql opcache zip

# Copia config do Opcache
COPY ./docker/php/opcache.ini /usr/local/etc/php/conf.d/opcache.ini

# Instala Composer globalmente
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Copia os arquivos do projeto (primeiro só o composer.json)
COPY composer.json composer.lock* /var/www/html/

WORKDIR /var/www/html

# Instala as dependências do Composer (se composer.lock existir)
RUN if [ -f composer.lock ]; then composer install --no-dev --optimize-autoloader; \
    else composer install --no-dev --optimize-autoloader --no-scripts; fi

# Copia o restante dos arquivos do projeto
COPY . /var/www/html/

# Garante permissões adequadas
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache