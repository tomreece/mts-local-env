#!/bin/bash

# Run JSUnit Tests
NODE_VER=8
#NOTE: Node version 10 or 12 results in 'Fatal error: Callback must be a function'

# Restore clean Magento
docker-compose down -v > /dev/null 2>&1
rm -rf html > /dev/null 2>&1
tar -xf install.tar
echo -e "\e[36mStarting docker containers\e[0m"
docker-compose up -d
docker ps -a
sleep 5s;
bash restoredb.sh

# Run Unit Tests
mv html/package.json.sample html/package.json
mv html/Gruntfile.js.sample html/Gruntfile.js

# Install NodeJS and Yarn
docker-compose exec -T fpm bash -c "cd /app; curl -sL https://deb.nodesource.com/setup_${NODE_VER}.x -o nodesource_setup.sh; bash nodesource_setup.sh"
docker-compose exec -T fpm bash -c 'cd /app; curl -sL https://dl.yarnpkg.com/debian/pubkey.gpg | sudo apt-key add - ; echo "deb https://dl.yarnpkg.com/debian/ stable main" | sudo tee /etc/apt/sources.list.d/yarn.list; apt-get update && apt-get install -y nodejs yarn'
# Install node_modules for Magento, grunt, jstestdriver, jshint
docker-compose exec -T fpm bash -c 'cd /app; npm install; npm install -g grunt-cli; npm install jit-grunt; npm install jstestdriver; npm install jshint'
# Run grunt spec
docker-compose exec -T fpm bash -c 'cd /app; echo "Node: `node --version`"; echo "NPM: `npm --version`"; grunt --version; grunt spec; echo "Exit Code: $?"'
# Copy reports to allure-report folder
docker-compose exec -T fpm bash -c 'cp /app/var/log/js-unit/*.xml /tmp/allure-report'

echo -e "\e[95mDone\e[0m"
