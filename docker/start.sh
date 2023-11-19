#!/bin/bash

service php8.0-fpm restart
service nginx restart

php artisan config:clear
php artisan config:cache
php artisan migrate --force
php artisan db:seed --force
php artisan script

ln -sf /proc/1/fd/1 /var/log/nginx/error.log

# Update nginx to match worker_processes to no. of cpu's
procs=$(cat /proc/cpuinfo | grep processor | wc -l)
sed -i -e "s/worker_processes  1/worker_processes $procs/" /etc/nginx/nginx.conf

# Always chown webroot for better mounting
chown -Rf nginx:nginx /usr/share/nginx/html

tail -f /dev/null
