#!/bin/bash

mysql -h${DB_HOST} --port=${DB_PORT} -u${DB_USERNAME} -p${DB_PASSWORD} -e "create database if not exists ${DB_DATABASE} CHARACTER SET utf8 COLLATE utf8_unicode_ci"

mv /var/www/html/.env.prod /var/www/html/.env
(cd /var/www/html && php artisan migrate --force && php artisan storage:link) & (nginx -g "daemon off;" & php-fpm)
