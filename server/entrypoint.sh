#!/bin/bash

echo -e "\n\n\n
=========================================================
|                                                       |
|                                                       |
|                 [DRUPAL] pre-flight                   |
|                                                       |
|                                                       |
=========================================================
\n"
echo "[PRE-FLIGHT] installing"
cd /workspaces
# set -a            
# source .env
# set +a

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

# echo "[NGINX] copy nginx config (already handled by devops)"
echo "[NGINX] copy nginx config"
if [ ! -f /etc/nginx/sites-available/drupal ]; then
    echo "copying nginx config"
    cp /workspaces/config/drupal.conf /etc/nginx/sites-available/drupal
    ln -s /etc/nginx/sites-available/drupal /etc/nginx/sites-enabled/
fi

echo "[DRUPAL] copying php config"
cp /workspaces/config/local.dev.settings.php /workspaces/web/web/sites/default/settings.php

# check if there is "core" folder on the /workspace/web
cd /workspaces/web
if [ ! -d /workspaces/web/web/core ]; then
    echo "[DRUPAL] downloading drupal core"
    composer install
else
    composer update
fi

# check permission on /workspaces/web if it's root change to www-data:www-data
# force to set www-data
echo "[DRUPAL] changing permission on /workspaces/web"
chown -R www-data:www-data /workspaces/web

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

install_error_message=$(/workspaces/web/vendor/bin/drush site-install minimal --db-url=$POSTGRES_CONNECTION_STRING --site-name="BRI Microsite Kartu Kredit" --account-name=$ADMIN_USER --account-pass=$ADMIN_PASS --config-dir=/workspaces/web/config/sync --existing-config -y 2>&1)
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

echo "[DRUPAL] import latest config"
/workspaces/web/vendor/bin/drush cim -y

echo -e "\n\n\n
=========================================================
|                                                       |
|                                                       |
|                  [DRUPAL] admin access                |
|                                                       |
|                                                       |
=========================================================

config username and password as follow :
u : $ADMIN_USER
p : $ADMIN_PASS
\n"

echo -e "\n\n\n
=========================================================
|                                                       |
|                                                       |
|                  [DRUPAL] finalize                    |
|                                                       |
|                                                       |
=========================================================
\n"
echo "[FINAL] checking nginx"
nginx -t
echo "[FINAL] starting nginx"
service nginx start
echo "[FINAL] starting php-fpm"
service php8.3-fpm start

echo -e "\n\n\n
=========================================================
|                                                       |
|                                                       |
|                  [DRUPAL] FINISHED                    |
|        executing sleep infinity to run the docker     |
|                                                       |
|                                                       |
=========================================================
\n"
sleep infinity
