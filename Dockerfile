FROM ubuntu:22.04

WORKDIR /workspaces
COPY . .

ENV DEBIAN_FRONTEND=noninteractive
RUN apt-get update -y
RUN apt-get install bash
RUN apt-get install -y software-properties-common curl zip unzip wget
RUN apt-get install ca-certificates apt-transport-https software-properties-common lsb-release -y \
    && add-apt-repository ppa:ondrej/php -y \
    && apt-get update -y \
    && apt-get install git php8.3 \
    php8.3-dev \
    php8.3-cli \
    php8.3-fpm \
    php8.3-common \
    php8.3-zip \
    php8.3-gd \
    php8.3-intl \
    php8.3-mbstring \
    php8.3-curl \
    php8.3-xml \
    php-pear \
    php8.3-tidy \
    php8.3-soap \
    php8.3-bcmath \
    php8.3-pgsql \
    php8.3-opcache \
    php-imagick \
    imagemagick \
    webp \ 
    php8.3-xmlrpc -y
RUN apt-get install nginx -y 
RUN apt-get install sudo -y

RUN rm /etc/nginx/sites-enabled/default
RUN git config --global --add safe.directory /workspaces
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
RUN chmod +x /workspaces/server/entrypoint.sh
RUN mkdir /workspaces/web/web/web
RUN ln -s /workspaces/web/web /workspaces/web/web/web/panel

EXPOSE 5000

CMD ["/workspaces/server/entrypoint.sh"]