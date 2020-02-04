#!/bin/bash

echo -e ${GROUP_NUMBER}

# Setup
docker-compose down -v > /dev/null 2>&1
rm -rf html > /dev/null 2>&1
tar -xf install.tar
echo -e "\e[36mStarting docker containers\e[0m"
docker-compose up -d
docker ps -a
sleep 5s;
bash restoredb.sh

# Run a specific group
docker-compose exec -T -e group=group${GROUP_NUMBER} fpm bash /tmp/files/perform_tests.sh

echo -e "\e[95mDone\e[0m"
