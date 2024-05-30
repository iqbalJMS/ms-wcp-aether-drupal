#!/bin/bash

rm -r /workspaces/web/vendor
rm -r /workspaces/web/web/core
rm -r /workspaces/web/web/modules/contrib
rm -r /workspaces/web/web/themes/contrib

echo "deleting individual files"
rm -r /workspaces/web/web/.csslintrc
rm -r /workspaces/web/web/.eslintignore
rm -r /workspaces/web/web/.eslintrc.json
rm -r /workspaces/web/web/.ht.router.php
rm -r /workspaces/web/web/.htaccess
rm -r /workspaces/web/web/autoload.php
rm -r /workspaces/web/web/example.gitignore
rm -r /workspaces/web/web/install.php
rm -r /workspaces/web/web/INSTALL.txt
rm -r /workspaces/web/web/README.md
rm -r /workspaces/web/web/tobots.txt
rm -r /workspaces/web/web/update.php
rm -r /workspaces/web/web/web.config
rm -r /workspaces/web/web/modules/README.md
rm -r /workspaces/web/web/themes/README.md

rm -r /workspaces/web/web/sites/default/default.services.yml
rm -r /workspaces/web/web/sites/default/default.settings.local.php
rm -r /workspaces/web/web/sites/default/settings.php

rm -r /workspaces/web/web/sites/development.services.yml
rm -r /workspaces/web/web/sites/example.settings.local.php
rm -r /workspaces/web/web/sites/example.sites.php
rm -r /workspaces/web/web/sites/README.md

