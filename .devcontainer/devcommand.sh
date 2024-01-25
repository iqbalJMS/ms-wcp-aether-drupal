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
echo "[PRE-FLIGHT] installing NVM for development"
cd ~
curl https://raw.githubusercontent.com/creationix/nvm/master/install.sh | bash
source ~/.bashrc
nvm install --latest
cd /workspaces
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
    cp /workspaces/config/drupal.conf /etc/nginx/sites-available/drupal
    ln -s /etc/nginx/sites-available/drupal /etc/nginx/sites-enabled/
fi

echo "[DRUPAL] copying php config"
cp /workspaces/config/local.dev.settings.php /workspaces/web/web/sites/default/settings.php

# check if there is "core" folder on the /workspace/web
if [ ! -d /workspaces/web/web/core ]; then
    echo "[DRUPAL] downloading drupal core"
    cd /workspaces/web
    composer install
fi

# create symlink folder between /workspaces/mount and /workspaces/web/web/sites/default/files
echo "[DRUPAL] mounting NFS"
ln -s /workspaces/mount/files /workspaces/web/web/sites/default

# check permission on /workspaces/web if it's root change to www-data:www-data
if [ "$(stat -c '%U' /workspaces/web/web)" = "root" ]; then
    echo "[DRUPAL] changing permission on /workspaces/web/web"
    chown -R www-data:www-data /workspaces/web/web
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
done < /workspaces/config.txt

install_error_message=$(/workspaces/web/vendor/bin/drush site:install standard --account-pass=$ADMIN_PASS -y 2>&1)
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
/workspaces/web/vendor/bin/drush theme:enable bri_main -y --debug
/workspaces/web/vendor/bin/drush theme:enable gin -y --debug
/workspaces/web/vendor/bin/drush config-set system.theme default bri_main -y --debug
/workspaces/web/vendor/bin/drush config-set system.theme admin gin -y --debug

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
    /workspaces/web/vendor/bin/drush en $line -y --debug
done < /workspaces/config/module.txt

echo "[DRUPAL] clear cache"
/workspaces/web/vendor/bin/drush cr
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
