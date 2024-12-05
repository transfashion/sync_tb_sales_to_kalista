#!/bin/bash

composer update

# copy config
cp config-development.php config-production.php


touch debug.txt
touch log.txt

chmod ugo+w debug.txt
chmod ugo+w log.txt

