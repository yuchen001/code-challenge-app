FROM webdevops/php-nginx:8.0-alpine

WORKDIR /app
COPY . .

RUN  set -eux; \
     composer install --no-scripts --optimize-autoloader --no-dev; \
     composer dump-autoload -o; \
     chown -R :application /app; \
     chmod -R 775 /app/storage /app/bootstrap/cache;
