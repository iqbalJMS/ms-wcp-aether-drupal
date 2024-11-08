#!/bin/bash

echo -e "\n
=========================================================
|                                                       |
|                                                       |
|                 [DRUPAL] pre-flight                   |
|                                                       |
|                                                       |
=========================================================
\n"
echo "[PRE-FLIGHT] installing NVM for installing"
cd ~
curl https://raw.githubusercontent.com/creationix/nvm/master/install.sh | bash
source ~/.bashrc
nvm install --latest
cd /workspaces

#set env
echo "[COMPOSER] set to use superuser"
export COMPOSER_ALLOW_SUPERUSER=1

# echo "[NGINX] we're not using nginx anymore, moving to native FPM"
echo "[NGINX] removing default site if exist"
# check if file exist
if [ -f /etc/nginx/sites-enabled/default ]; then
    echo "default site exist"
    rm /etc/nginx/sites-enabled/default
fi
echo "[NGINX] copy nginx config (already handled by devops)"
# dont copy if exit
#================================================================================
#                                                                               #
#  if [ ! -f /etc/nginx/sites-available/drupal.conf ]; then                     #
#      echo "copying nginx config"                                              #
#      cp /workspaces/config/drupal.conf /etc/nginx/sites-available/drupal.conf #
#      ln -s /etc/nginx/sites-available/drupal.conf /etc/nginx/sites-enabled/   #
#  fi                                                                           #
#================================================================================

echo "[DRUPAL] copying php config"
cp /workspaces/config/local.indesc.settings.php /workspaces/web/web/sites/default/settings.php

# check if there is "core" folder on the /workspace/web
cd /workspaces/web
if [ ! -d /workspaces/web/web/core ]; then
    echo "[DRUPAL] downloading drupal core"
    composer install
else
    composer update
fi

# create symlink folder between /workspaces/mount and /workspaces/web/web/sites/default/files
echo "[DRUPAL] mounting NFS"
ln -s /workspaces/mount/files /workspaces/web/web/sites/default/files/uploads

# check permission on /workspaces/web if it's root change to www-data:www-data
# force to set www-data
echo "[DRUPAL] changing permission on /workspaces/web"
sudo chown -R www-data:www-data /workspaces/web

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
ADMIN_USER="gumini"
DB_CONNECTION="pgsql://db_ms_wcp_aether_drupal_owner:N7aMbnzSdw3t@ep-ancient-field-a1xfmqiy.ap-southeast-1.aws.neon.tech/db_ms_wcp_aether_drupal?sslmode=require"

install_error_message=$(/workspaces/web/vendor/bin/drush site-install minimal --db-url=$DB_CONNECTION --site-name="BRI Microsite Kartu Kredit" --account-name=$ADMIN_USER --account-pass=$ADMIN_PASS --config-dir=/workspaces/web/config/sync --existing-config -y 2>&1)
substring="AlreadyInstalledException"
if [[ "$install_error_message" == *"$substring"* ]]; then
    echo "[DRUPAL] already had database, skip database proccess"
else
    echo "[DRUPAL] database initiated"
    echo -e "\n\n If has error, print Messages: \n\n\n"
    echo $install_error_message
fi

echo "[DRUPAL] clear cache"
/workspaces/web/vendor/bin/drush cr

echo -e "\n\n\n
=========================================================
|                                                       |
|                                                       |
|                  [DRUPAL] admin acc                   |
|                                                       |
|                                                       |
=========================================================

config username and password as follow :
u : $ADMIN_USER
p : $ADMIN_PASS
\n"
