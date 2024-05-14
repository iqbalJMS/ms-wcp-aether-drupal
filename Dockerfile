FROM php:fpm-alpine3.19
WORKDIR /workspaces
COPY . .
RUN apk add curl zip unzip wget
RUN apk add git php8.3-dev \
                       php8.3-common \
                       php8.3-zip \
                       php8.3-gd \
                       php8.3-intl \
                       php8.3-mbstring \
                       php8.3-curl \
                       php8.3-xml \
                       php8.3-tidy \
                       php8.3-soap \
                       php8.3-bcmath \
                       php8.3-pgsql \
                       php8.3-opcache \
                       php-pear \
                       php-imagick \
                       php-xmlrpc -y \
                       imagemagick \
                       webp \ 
# RUN git config --global --add safe.directory /workspaces
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
EXPOSE 9000
RUN echo "true" > /workspaces/INIT.txt
RUN chmod +x ./.devcontainer/command.sh
RUN chmod +x ./.devcontainer/entrypoint.sh
