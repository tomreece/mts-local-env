#!/bin/bash

# Starts or stops the Magento containers
# Use start, stop, or restart arguments

ARG=$1
shopt -s nocasematch
clear

if [[ "X${ARG}" != "Xstop" ]] &&  [[ "X${ARG}" != "Xstart" ]] &&  [[ "X${ARG}" != "Xrestart" ]]; then
  echo "--- Please specify [stop/start/restart] argument ---"
  echo "--- Stop and destroy all containers - [$0 stop] ---"
  echo "--- Restore from install.tar and start all containers - [$0 start] ---"
  exit 1
fi

if [[ ${ARG} == "stop" ]] || [[ "X${ARG}" == "Xrestart" ]]; then
  echo -e "--- Stopping containers ---"
  docker-compose down -v
fi

if [[ ${ARG} == "start" ]] || [[ "X${ARG}" == "Xrestart" ]]; then
  echo -e "--- Restoring and starting containers ---"
  docker-compose down -v > /dev/null 2>&1
  rm -rf html > /dev/null 2>&1
  tar -xf install.tar
  docker-compose up -d
  docker ps -a
  sleep 5s;
  bash restoredb.sh
  docker-compose exec -T fpm bash /tmp/files/perform_setup_only.sh
fi

shopt -u nocasematch
echo -e "--- Done ---"
