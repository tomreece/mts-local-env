#!/bin/bash
# Restores database after install.tar is extracted
CHECK_EXIT=1
COUNTER=1
MAX=5
echo -e "\e[34;1mRestoring Magento DB\e[0m"
while [ ${CHECK_EXIT} -ne 0 ] && [ ${COUNTER} -le ${MAX} ]; do
  echo "Attempt ${COUNTER}/${MAX}"
  sleep "$((2**COUNTER))"
  docker-compose exec -T mariadb bash -c 'mysql -u root magento < /tmp/html/magentodump.sql'
  CHECK_EXIT=$?
  (( COUNTER++ )) 
done
exit ${CHECK_EXIT}
