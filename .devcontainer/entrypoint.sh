#!/bin/bash
echo "starting nginx"
service nginx start
echo "starting php-fpm"
service php8.3-fpm start