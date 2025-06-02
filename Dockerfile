FROM ronaregen/php:frankenphp-latest AS main


COPY . /app
COPY entrypoint.sh /usr/local/bin/entrypoint.sh

RUN composer install

RUN chmod +x /usr/local/bin/entrypoint.sh
ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]
