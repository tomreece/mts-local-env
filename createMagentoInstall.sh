#!/bin/bash

# Start containers
docker-compose up -d
sleep 5s

# Install Magento
docker-compose exec -T fpm bash /tmp/files/perform_install.sh

# Peforms mysqldump and creates install.tar from html folder
CHECK_EXIT=1
COUNTER=1
MAX=5
echo -e "--- Dumping Magento DB ---"
while [ ${CHECK_EXIT} -ne 0 ] && [ ${COUNTER} -le ${MAX} ]; do
  echo "--- Attempt ${COUNTER}/${MAX} ---"
  sleep "$((2**COUNTER))"
  docker-compose exec -T mariadb bash -c 'mysqldump -u root magento > /tmp/html/magentodump.sql'
  CHECK_EXIT=$?
  (( COUNTER++ )) 
done

rm -f install.tar > /dev/null 2>&1
# Stop containers
docker-compose down -v
# Remove any .cov files that got generated for whatever reason. I don't know why there are some generated before tests are run.
#rm html/pub/cov/*
# Create the install.tar file for step 2
tar -cf install.tar html
echo -e "--- Magento install.tar file ready ---"
