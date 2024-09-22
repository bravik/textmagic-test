#########################################################################
# This Dockerfile is used for building both prod and dev images using multi-stages builds
#########################################################################

FROM composer:2 AS composer
FROM ghcr.io/roadrunner-server/roadrunner:2024.2 AS roadrunner


################################################################################
# BASE LAYER
################################################################################

FROM php:8.3-cli-alpine AS base

COPY --chown=0:33 --from=roadrunner /usr/bin/rr /usr/local/bin/rr

# Install prerequisites
RUN apk --update add --no-cache $PHPIZE_DEPS \
    bash make linux-headers \
    procps \
    lsb-release \
    iputils-ping \
    curl \
    # Postgres client
    libpq-dev \
    && docker-php-ext-install  pdo_pgsql pgsql \
    # Cleanup
    && apk del gcc gmp g++ musl-dev libc-dev dpkg re2c m4 autoconf dpkg-dev dpkg \
    && rm -rf /var/cache/apk/*


FROM base AS base-with-composer

# Install git and unzip for composer
RUN apk --update add --no-cache $PHPIZE_DEPS \
    unzip \
    git \
    # Cleanup
    && rm -rf /var/cache/apk/*

# Install PHP composer:
ENV COMPOSER_HOME /tmp/composer
COPY --from=composer /usr/bin/composer /usr/local/bin

# Configure composer AUTH to be able to access private composer repositories
# The composer_auth_token secret is provided by
# - docker-compose.yml as build secret
# - github actions workflow in Build & Push action
USER 33:33


USER 0
################################################################################
# DEVELOPMENT IMAGE
################################################################################

FROM base-with-composer AS dev

ENV APP_ENV=dev

# Create working directory. Ownership will be changed in entrypoint.sh which
# executes *after* the volume has been mounted.
RUN mkdir /app

# Copy entrypoint script into container, make it executable, then make it the entrypoint for the container:
COPY entrypoint.sh /entrypoint.sh
RUN chmod +x /entrypoint.sh
# Option '+x' adds executable flag to the file

# Create non-root user
RUN addgroup -g 1000 dev && \
    adduser -u 1000 -G dev -s $(which bash) -D dev
# -u UID: set the user-id.
# -G GROUP: add the user to GROUP.
# -s SHELL: set the user's shell.
# -D: don't assign a password for this user.

# Fix permissions
RUN mkdir -p /tmp/composer && chown dev:dev /tmp/composer -R

USER 1000:1000

ENTRYPOINT ["/entrypoint.sh"]

