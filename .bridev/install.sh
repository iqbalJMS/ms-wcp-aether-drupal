export DEBIAN_FRONTEND=noninteractive
sudo apt update -y
sudo apt install -y software-properties-common
sudo apt install -y curl zip unzip wget
sudo clean -y && rm -rf /var/lib/apt/lists/*
sudo apt update -y
sudo apt update -y
sudo apt dist-upgrade -y
sudo apt install -y ca-certificates apt-transport-https software-properties-common lsb-release
sudo add-apt-repository ppa:ondrej/php -y
sudo apt update -y
sudo apt upgrade -y \
sudo apt install git php8.3 \
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
sudo service php8.3-fpm start
#run other sh
sh ../.devcontainer/devcommand.sh