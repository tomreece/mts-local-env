#!/bin/bash

# Run MFTF by individual tests
# Place all of your test names in the mftf-test-list.txt file

if [ ! -f mftf-test-list.txt ]; then
  echo -e "--- The required mftf-test-list.txt file is missing ---"
  exit 1
fi

clear
echo -e "--- Found MFTF tests: \n$(grep -v '^#' mftf-test-list.txt)\n ---"


# Restore clean Magento before each group
docker-compose down -v > /dev/null 2>&1
rm -rf html > /dev/null 2>&1
tar -xf install.tar
echo -e "--- Starting docker containers ---"
docker-compose up -d
docker ps -a
sleep 5s;
bash restoredb.sh

cp mftf-test-list.txt html/mftf-test-list.txt

# Run MFTF for specific group
docker-compose exec -T -e group=${GROUP} fpm bash /tmp/files/perform_tests.sh

echo -e "--- Done ---"
