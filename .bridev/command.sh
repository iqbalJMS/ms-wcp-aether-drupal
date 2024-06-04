#!/bin/bash

#prompt for folder param
echo "Enter the folder name (absolute path) for the project"
read folder
# set folder to os env
export FOLDER=$folder
echo "true" > $FOLDER/INIT.txt

echo -e "\n\n\n
=========================================================
|                                                       |
|                                                       |
|                 [DRUPAL] pre-flight                   |
|                                                       |
|                                                       |
=========================================================
\n"
echo "[PRE-FLIGHT] installing NVM for development"
cd ~
curl https://raw.githubusercontent.com/creationix/nvm/master/install.sh | bash
source ~/.bashrc
nvm install --latest
# cd to FOLDER
cd $FOLDER
echo "[NGINX] removing default site if exist"
# check if file exist
if [ -f /etc/nginx/sites-enabled/default ]; then
    echo "default site exist"
    rm /etc/nginx/sites-enabled/default
fi
echo "[NGINX] copy nginx config"
# dont copy if exit
if [ ! -f /etc/nginx/sites-available/drupal ]; then
    echo "copying nginx config"
    sudo cp $FOLDER/config/drupal.bridev.conf /etc/nginx/sites-available/drupal
    sudo ln -s /etc/nginx/sites-available/drupal /etc/nginx/sites-enabled/
fi

echo "[DRUPAL] copying php config"
sudo cp $FOLDER/config/local.bridev.settings.php $FOLDER/web/web/sites/default/settings.php

# check if there is "core" folder on the /workspace/web
if [ ! -d $FOLDER/web/web/core ]; then
    echo "[DRUPAL] downloading drupal core"
    cd $FOLDER/web
    composer install
fi

# create symlink folder between $FOLDER/mount and $FOLDER/web/web/sites/default/files
echo "[DRUPAL] mounting NFS"
sudo ln -s $FOLDER/mount/files $FOLDER/web/web/sites/default

# check permission on $FOLDER/web if it's root change to www-data:www-data
if [ "$(stat -c '%U' $FOLDER/web/web)" = "root" ]; then
    echo "[DRUPAL] changing permission on $FOLDER/web/web"
    sudo chown -R www-data:www-data $FOLDER/web/web
fi

echo -e "\n\n\n
=========================================================
|                                                       |
|                                                       |
|            [DRUPAL] database init proccess            |
|                                                       |
|                                                       |
=========================================================
\n"
ADMIN_PASS="default"
# check if there is INSTALLED.xt on /workspace/config
while IFS= read -r line; do
    if [[ $line == *"ADMIN_PASS"* ]]; then
        ADMIN_PASS=$(echo $line | cut -d'=' -f2)
        # clean up " and ' from ADMIN_PASS
        ADMIN_PASS=$(echo $ADMIN_PASS | tr -d '"' | tr -d "'")
    fi
done < $FOLDER/config.txt

install_error_message=$($FOLDER/web/vendor/bin/drush site:install standard --account-pass=$ADMIN_PASS -y 2>&1)
substring="AlreadyInstalledException"
if [[ "$install_error_message" == *"$substring"* ]]; then
  echo "[DRUPAL] already had database, skip database proccess"
else
  echo "[DRUPAL] database initiated"
fi

echo -e "\n\n\n
=========================================================
|                                                       |
|                                                       |
|                  [DRUPAL] set theme                   |
|                                                       |
|                                                       |
=========================================================
\n"
$FOLDER/web/vendor/bin/drush theme:enable bri_main -y --debug
$FOLDER/web/vendor/bin/drush theme:enable gin -y --debug
$FOLDER/web/vendor/bin/drush config-set system.theme default bri_main -y --debug
$FOLDER/web/vendor/bin/drush config-set system.theme admin gin -y --debug

echo -e "\n\n\n
=========================================================
|                                                       |
|                                                       |
|                 [DRUPAL] set module                   |
|                                                       |
|                                                       |
=========================================================
\n"
# read config/module.txt line by line
while IFS= read -r line; do
    echo "[DRUPAL] module installing $line"
    $FOLDER/web/vendor/bin/drush en $line -y
done < $FOLDER/config/module.txt

echo "[DRUPAL] clear cache"
$FOLDER/web/vendor/bin/drush cr
echo -e "\n\n\n
=========================================================
|                                                       |
|                                                       |
|                  [DRUPAL] finalize                    |
|                                                       |
|                                                       |
=========================================================
\n"
echo "[FINAL] starting nginx"
service nginx start
echo "[FINAL] starting php-fpm"
service php8.3-fpm start
