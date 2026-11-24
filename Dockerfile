FROM php:8.4.27-cli

ARG UID=1000
ARG GID=1000

COPY --from=mlocati/php-extension-installer:2 /usr/bin/install-php-extensions /usr/local/bin/
RUN install-php-extensions pdo_pgsql pgsql pdo_mysql zip intl bcmath pcntl

RUN apt-get update \
    && apt-get install -y --no-install-recommends git unzip curl \
    && rm -rf /var/lib/apt/lists/*

COPY --from=composer:2 /usr/bin/composer /usr/local/bin/composer

RUN getent group "${GID}" >/dev/null || groupadd -g "${GID}" appgroup \
    && id -u "${UID}" >/dev/null 2>&1 || useradd -u "${UID}" -g "${GID}" -m -s /bin/sh appuser

WORKDIR /app

COPY docker/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

RUN mkdir -p /app/vendor /app/storage /app/bootstrap/cache \
    && chown -R ${UID}:${GID} /app

USER ${UID}:${GID}

EXPOSE 8000

ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]
CMD ["php", "-S", "0.0.0.0:8000", "-t", "/app/public", "/app/vendor/laravel/framework/src/Illuminate/Foundation/resources/server.php"]
