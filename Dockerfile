FROM ronaregen/php:frankenphp-latest  AS vendor

WORKDIR /app

COPY composer.json composer.lock helpers.php /app/

RUN composer install --no-scripts

# -----------------------------------------------
FROM node:lts-alpine AS frontend

WORKDIR /app

COPY  . /app
COPY --from=vendor /app/vendor /app/vendor

RUN npm install && npm run build && rm -rf node_modules

FROM ronaregen/php:frankenphp-latest AS main


COPY --from=frontend /app /app
COPY entrypoint.sh /usr/local/bin/entrypoint.sh

RUN chmod +x /usr/local/bin/entrypoint.sh
ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]
