#!/bin/bash

# Run MFTF for parallel groups on a single instance.
# Note: This runs each group one after another in series not parallel 
# For a specific group (i.e. group7) use START_GROUP=7 and END_GROUP=7

TOTAL_GROUPS=$(ls html/dev/tests/acceptance/tests/functional/Magento/FunctionalTest/_generated/groups/  | wc -l)
START_GROUP=1
END_GROUP=${TOTAL_GROUPS}

clear
echo -e "--- Running MFTF groups ${START_GROUP} to ${END_GROUP} ---"

for ((i=START_GROUP;i<=END_GROUP;i++)); do

# Restore clean Magento before each group
docker-compose down -v > /dev/null 2>&1
rm -rf html > /dev/null 2>&1
tar -xf install.tar
echo -e "--- Starting docker containers ---"
docker-compose up -d
docker ps -a
sleep 5s;
bash restoredb.sh

# Run MFTF for specific group
GROUP="group${i}"
export GROUP
docker-compose exec -T -e group=${GROUP} fpm bash /tmp/files/perform_tests.sh
done

echo -e "--- Done ---"
