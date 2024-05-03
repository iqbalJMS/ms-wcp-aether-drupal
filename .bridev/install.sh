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
sudo apt upgrade -y
sudo apt install -y git 
sudo apt install -y php8.3
sudo apt install -y php8.3-dev 
sudo apt install -y php8.3-cli 
sudo apt install -y php8.3-fpm 
sudo apt install -y php8.3-common 
sudo apt install -y php8.3-zip 
sudo apt install -y php8.3-gd 
sudo apt install -y php8.3-intl 
sudo apt install -y php8.3-mbstring 
sudo apt install -y php8.3-curl 
sudo apt install -y php8.3-xml 
sudo apt install -y php-pear 
sudo apt install -y php8.3-tidy 
sudo apt install -y php8.3-soap 
sudo apt install -y php8.3-bcmath 
sudo apt install -y php8.3-pgsql 
sudo apt install -y php8.3-opcache 
sudo apt install -y php-imagick 
sudo apt install -y imagemagick 
sudo apt install -y webp
sudo apt install -y php-xmlrpc
sudo apt install -y nginx
sudo pecl install mongodb
sudo echo "extension=mongodb.so" >> /etc/php/8.3/cli/php.ini
sudo curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

sudo service php8.3-fpm start
# run other sh
sh ./.bridev/command.sh