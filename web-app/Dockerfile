FROM php:7.4-fpm-alpine

ARG UID
ARG GID

ENV UID=${UID}
ENV GID=${GID}

RUN mkdir -p /var/www/html

WORKDIR /var/www/html

COPY --from=composer:latest /usr/bin/composer /usr/local/bin/composer

# Copy package.json and package-lock.json
COPY package*.json ./


# MacOS staff group's gid is 20, so is the dialout group in alpine linux. We're not using it, let's just remove it.
RUN delgroup dialout

RUN addgroup -g ${GID} --system laravel
RUN adduser -G laravel --system -D -s /bin/sh -u ${UID} laravel

RUN sed -i "s/user = www-data/user = laravel/g" /usr/local/etc/php-fpm.d/www.conf
RUN sed -i "s/group = www-data/group = laravel/g" /usr/local/etc/php-fpm.d/www.conf
RUN echo "php_admin_flag[log_errors] = on" >> /usr/local/etc/php-fpm.d/www.conf

RUN docker-php-ext-install pdo pdo_mysql

RUN mkdir -p /usr/src/php/ext/redis \
    && curl -L https://github.com/phpredis/phpredis/archive/5.3.4.tar.gz | tar xvz -C /usr/src/php/ext/redis --strip 1 \
    && echo 'redis' >> /usr/src/php-available-exts \
    && docker-php-ext-install redis

# RUN apk add --update --no-cache libgd libpng-dev libjpeg-turbo-dev freetype-dev

RUN apk --update --no-cache add \
        libpng \
        libjpeg-turbo \
        freetype \
        libgd \
        libpng-dev \
        libjpeg-turbo-dev \
        freetype-dev \
    && docker-php-ext-configure gd \
        --with-freetype \
        --with-jpeg \
    && docker-php-ext-install gd \
    && apk del \
        libpng-dev \
        libjpeg-turbo-dev \
        freetype-dev

RUN apk --update --no-cache add \
        libzip-dev \
        zip \
  && docker-php-ext-install zip


# Install bash
RUN apk add --no-cache bash

# # Install Node.js
RUN apk add --update nodejs npm && \
    npm install -g n && \
    n 12.16.2

# Change ownership of the Node.js directories to laravel user
RUN chown -R laravel:laravel /usr/local/lib/node_modules /usr/local/bin /usr/local/share /usr/local/n

# After copying your application's source code and before switching to the non-root user
RUN chown -R laravel:laravel /var/www/html

# RUN docker-php-ext-install -j$(nproc) gd
    
#RUN ["chmod", "+x", "/var/www/html/entrypoint.sh"]
ENTRYPOINT ["sh","/var/www/html/entrypoint.sh"]

USER laravel

CMD ["php-fpm", "-y", "/usr/local/etc/php-fpm.conf", "-R"]