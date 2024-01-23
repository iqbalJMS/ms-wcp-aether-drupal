#!/bin/bash

# check if there is INIT.txt on /workspaces
if [ -f /workspaces/mount/INIT.txt ]; then
    # check the value of INIT.txt
    if [ "$(cat /workspaces/mount/INIT.txt)" = "true" ]; then
        # run ./command.sh
        echo "running command.sh"
        /workspaces/.devcontainer/devcommand.sh
    fi
    # set INIT.txt to false
    echo "false" > /workspaces/mount/INIT.txt
fi

echo "starting nginx"
service nginx start
echo "starting php-fpm"
service php8.3-fpm start

sleep infinity