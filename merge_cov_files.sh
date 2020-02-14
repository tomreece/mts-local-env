#!/bin/bash

# This script expects that containers from previous steps are still running

echo -e "--- Merging cov files ---"

# Run a specific group
docker-compose exec -T -e GROUP_NUMBER=${GROUP_NUMBER} fpm bash /tmp/files/merge_cov_files.sh
