#!/bin/bash

# Run Unit Tests

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
# TODO: dynamically determine testsuites
docker-compose exec -T fpm bash -c '/app/vendor/phpunit/phpunit/phpunit -c /app/dev/tests/unit/phpunit.xml.dist --testsuite Magento_Unit_Tests_App_Code --log-junit /tmp/allure-report/Magento_Unit_Tests_App_Code_result.xml'
docker-compose exec -T  fpm bash -c '/app/vendor/phpunit/phpunit/phpunit -c /app/dev/tests/unit/phpunit.xml.dist --testsuite Magento_Unit_Tests_Other --log-junit /tmp/allure-report/Magento_Unit_Tests_Other_result.xml'

echo -e "\e[95mDone\e[0m"
