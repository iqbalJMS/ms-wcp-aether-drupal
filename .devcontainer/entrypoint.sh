#!/bin/bash

# check if there is INIT.txt on /workspaces
echo "running entrypoint" 
if [ -f /workspaces/INIT.txt ]; then
    echo "INIT file FOUND" 
    # check the value of INIT.txt
    if [ "$(cat /workspaces/INIT.txt)" = "true" ]; then
        # run ./command.sh
        echo "running command.sh"
        /workspaces/.devcontainer/devcommand.sh
    fi
    # set INIT.txt to false
    echo "saving INIT.txt to false" 
    echo "false" > /workspaces/INIT.txt
fi

echo "starting nginx"
service nginx start
echo "starting php-fpm"
service php8.3-fpm start

# sleep infinity