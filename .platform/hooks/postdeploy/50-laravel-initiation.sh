#!/usr/bin/bash

cp /var/app/current/.env.example /var/app/current/.env

php /var/app/current/artisan key:generate

php /var/app/current/artisan migrate

chown -R webapp:webapp /var/app/current

