#!/bin/bash
echo "removing default site if exist"
# check if file exist
if [ -f /etc/nginx/sites-enabled/default ]; then
    echo "default site exist"
    rm /etc/nginx/sites-enabled/default
fi
echo "copying php config"
cp /workspaces/config/local.dev.settings.php /workspaces/web/web/sites/default/settings.php

echo "copy nginx config"
# dont copy if exit
if [ ! -f /etc/nginx/sites-available/drupal ]; then
    echo "copying nginx config"
    cp /workspaces/config/drupal.conf /etc/nginx/sites-available/drupal
    ln -s /etc/nginx/sites-available/drupal /etc/nginx/sites-enabled/
fi

# check if there is "core" folder on the /workspace/web
if [ ! -d /workspaces/web/web/core ]; then
    echo "downloading drupal core"
    cd /workspaces/web
    composer install
fi

# create symlink folder between /workspaces/mount and /workspaces/web/web/sites/default/files
ln -s /workspaces/mount/files /workspaces/web/web/sites/default


# check permission on /workspaces/web if it's root change to www-data:www-data
if [ "$(stat -c '%U' /workspaces/web/web)" = "root" ]; then
    echo "changing permission on /workspaces/web/web"
    chown -R www-data:www-data /workspaces/web/web
fi

apt install tree

echo "checking workspaces"
ls -a /workspaces
tree /workspaces -L 2

echo "checking root"
ls -a /


# check if there is INSTALLED.xt on /workspace/config
if [ ! -f /workspaces/mount/INSTALLED.txt ]; then
    # read env.txt line by line
    # find ADMIN_PASS and set it to admin password
    while IFS= read -r line; do
        if [[ $line == *"ADMIN_PASS"* ]]; then
            echo "setting admin password"
            ADMIN_PASS=$(echo $line | cut -d'=' -f2)
            # clean up " and ' from ADMIN_PASS
            ADMIN_PASS=$(echo $ADMIN_PASS | tr -d '"' | tr -d "'")
            /workspaces/web/vendor/bin/drush site:install standard --account-pass=$ADMIN_PASS -y
        fi
    done < /workspaces/.env
    echo "creating INSTALLED.txt"
    touch /workspaces/mount/INSTALLED.txt
fi

# read config/module.txt line by line
while IFS= read -r line; do
    echo "installing $line"
    /workspaces/web/vendor/bin/drush en $line -y
done < /workspaces/config/module.txt

echo "set admin theme"
/workspaces/web/vendor/bin/drush theme:enable gin -y
/workspaces/web/vendor/bin/drush config-set system.theme admin gin -y

echo "clear cache"
/workspaces/web/vendor/bin/drush rc
