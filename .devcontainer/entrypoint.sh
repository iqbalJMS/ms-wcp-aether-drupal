#!/bin/bash

# check if there is INIT.txt on /workspaces
echo "running entrypoint" 
if [ -f /workspaces/INIT.txt ]; then
    echo "INIT file FOUND" 
    # check the value of INIT.txt
    if [ "$(cat /workspaces/INIT.txt)" = "true" ]; then
        # run ./command.sh
        echo "running command.sh"
        /workspaces/.devcontainer/command.sh
    fi
    # set INIT.txt to false
    echo "saving INIT.txt to false" 
    echo "false" > /workspaces/INIT.txt
fi
echo -e "\n\n\n
=========================================================
|                                                       |
|                                                       |
|                  [DRUPAL] finalize                    |
|                                                       |
|                                                       |
=========================================================
\n"
# echo "[FINAL] checking nginx"
# nginx -t
# echo "[FINAL] starting nginx"
# service nginx start
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
