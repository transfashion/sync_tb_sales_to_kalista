#!/bin/bash

# kirim data sales dari TransBrowser ke kalista
# Agung Nugroho <agung@transfashionindonesia.com>
# Created at 5 Desember 2024

cmd="php start.php --config=config-production.php"

echo "start sending data..."
echo $cmd
$cmd

