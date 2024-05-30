FROM ubuntu:22.04
RUN apt-get update && export DEBIAN_FRONTEND=noninteractive \
    && apt-get install -y software-properties-common \
    && apt-get clean -y && rm -rf /var/lib/apt/lists/*
RUN apt-get update && export DEBIAN_FRONTEND=noninteractive \
    && apt-get install -y curl zip unzip wget \
    && apt-get clean -y && rm -rf /var/lib/apt/lists/*
RUN apt-get update -y \
    && apt-get dist-upgrade -y \
    && export DEBIAN_FRONTEND=noninteractive \
    && apt-get install ca-certificates apt-transport-https software-properties-common lsb-release -y \
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
RUN git config --global --add safe.directory /workspaces
RUN apt-get update -y \
    && export DEBIAN_FRONTEND=noninteractive \
    && apt-get install nginx -y 
RUN rm /etc/nginx/sites-enabled/default
RUN ln -s /etc/nginx/sites-available/drupal /etc/nginx/sites-enabled/
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
# Set the entrypoint script
RUN echo "true" > /workspaces/INIT.txt
CMD ["./server/entrypoint.sh"]
