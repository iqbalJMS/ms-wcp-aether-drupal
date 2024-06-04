#!/bin/bash
echo "Enter the folder name (absolute path) for the project"
read folder
export FOLDER=$folder

rm -r $FOLDER/web/vendor
rm -r $FOLDER/web/web/core
rm -r $FOLDER/web/web/modules/contrib
rm -r $FOLDER/web/web/themes/contrib

echo "deleting individual files"
rm -r $FOLDER/web/web/.csslintrc
rm -r $FOLDER/web/web/.eslintignore
rm -r $FOLDER/web/web/.eslintrc.json
rm -r $FOLDER/web/web/.ht.router.php
rm -r $FOLDER/web/web/.htaccess
rm -r $FOLDER/web/web/autoload.php
rm -r $FOLDER/web/web/example.gitignore
rm -r $FOLDER/web/web/install.php
rm -r $FOLDER/web/web/INSTALL.txt
rm -r $FOLDER/web/web/README.md
rm -r $FOLDER/web/web/robots.txt
rm -r $FOLDER/web/web/update.php
rm -r $FOLDER/web/web/web.config
rm -r $FOLDER/web/web/index.php
rm -r $FOLDER/web/composer.lock
rm -r $FOLDER/web/web/modules/README.txt
rm -r $FOLDER/web/web/themes/README.txt
rm -r $FOLDER/web/web/sites/default/default.settings.php
rm -r $FOLDER/web/web/sites/default/files
rm -r $FOLDER/web/web/profiles/README.txt

rm -r $FOLDER/web/web/sites/default/default.services.yml
rm -r $FOLDER/web/web/sites/default/default.settings.local.php
rm -r $FOLDER/web/web/sites/default/settings.php

rm -r $FOLDER/web/web/sites/development.services.yml
rm -r $FOLDER/web/web/sites/example.settings.local.php
rm -r $FOLDER/web/web/sites/example.sites.php
rm -r $FOLDER/web/web/sites/README.txt

