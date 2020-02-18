#!/bin/bash

echo -e "--- Preparing to run group ${GROUP_NUMBER} ---"

# Setup Magento
docker-compose down -v > /dev/null 2>&1
rm -rf html > /dev/null 2>&1
tar -xf install.tar
echo -e "--- Starting docker containers ---"
docker-compose up -d
docker ps -a
sleep 5s;
bash restoredb.sh

# Run a specific group
docker-compose exec -T -e group=group${GROUP_NUMBER} fpm bash /tmp/files/perform_tests.sh

echo -e "--- Done ---"
