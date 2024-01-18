echo "removing default site if exist"
# check if file exist
if [ -f /etc/nginx/sites-enabled/default ]; then
    echo "default site exist"
    rm /etc/nginx/sites-enabled/default
fi
echo "copy nginx config"
# dont copy if exit
if [ ! -f /etc/nginx/sites-available/drupal ]; then
    echo "copying nginx config"
    cp /workspaces/config/drupal.conf /etc/nginx/sites-available/drupal
    ln -s /etc/nginx/sites-available/drupal /etc/nginx/sites-enabled/
fi
# check if there is "core" folder on the /workspace/web
if [ ! -d /workspaces/web/core ]; then
    echo "downloading drupal core"
    cd /workspaces/web
    composer install
fi
# check permission on /workspaces/web if it's root change to www-data:www-data
if [ "$(stat -c '%U' /workspaces/web)" = "root" ]; then
    echo "changing permission on /workspaces/web"
    chown -R www-data:www-data /workspaces/web
fi

echo "starting nginx"
service nginx start
echo "starting php-fpm"
service php8.3-fpm start