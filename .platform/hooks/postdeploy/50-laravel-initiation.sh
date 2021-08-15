#!/usr/bin/bash

sudo chown -R webapp:webapp /var/app/current

sudo chmod -R 777 /var/app/current/storage

cp /var/app/current/.env.example /var/app/current/.env

php /var/app/current/artisan key:generate

php /var/app/current/artisan migrate



