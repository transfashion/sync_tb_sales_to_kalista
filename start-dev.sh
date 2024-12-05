#!/bin/bash

cmd="php ./start.php --config=config-development.php"

echo "start sending data..."
echo $cmd
$cmd
