# 1) Build dos assets (Vite)
FROM node:20-alpine AS node_build
WORKDIR /app

COPY package*.json ./
RUN npm ci

COPY . .
RUN npm run build


# 2) App PHP (Laravel)
FROM php:8.2-cli

RUN apt-get update && apt-get install -y \
    git unzip libpq-dev \
 && docker-php-ext-install pdo pdo_pgsql \
 && rm -rf /var/lib/apt/lists/*

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www

# Copia o projeto todo (sem volumes = menos dor pro recrutador)
COPY . /var/www

# Copia os assets buildados (public/build)
COPY --from=node_build /app/public/build /var/www/public/build

# Permissões mínimas pro Laravel escrever logs/cache
RUN mkdir -p storage/logs bootstrap/cache \
 && chmod -R 775 storage bootstrap/cache

EXPOSE 8000

CMD sh -lc "\
  composer install --no-interaction --prefer-dist --optimize-autoloader && \
  if [ ! -f .env ]; then cp .env.example .env; fi && \
  php -r \"exit(strpos(file_get_contents('.env'),'APP_KEY=base64:')!==false?0:1);\" || php artisan key:generate --force && \
  php artisan migrate --force || true && \
  php artisan serve --host=0.0.0.0 --port=8000 \
"