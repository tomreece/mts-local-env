#!/bin/bash
export TERM='dumb'
magento_path='/app'
COLOR_BLUE='\e[34;1m'
COLOR_RESET='\e[0m'

php -v

chown -R www-data:www-data ${magento_path}

# Enable Redis
echo -e "--- Enabling Redis ---"
php ${magento_path}/bin/magento -n setup:config:set --cache-backend=redis --cache-backend-redis-server=redis --cache-backend-redis-db=0
php ${magento_path}/bin/magento -n setup:config:set --page-cache=redis --page-cache-redis-server=redis --page-cache-redis-db=1
php ${magento_path}/bin/magento -n setup:config:set --session-save=redis --session-save-redis-host=redis --session-save-redis-log-level=3 --session-save-redis-db=2

# Enable RabbitMQ 
echo -e "--- Enabling RabbitMQ ---"
php ${magento_path}/bin/magento -n setup:config:set --amqp-host="rabbitmq" --amqp-port="5672" --amqp-user="rabbitmq" --amqp-password="rabbitmq_password" --amqp-virtualhost="/"

# Enable Elasticsearch 6
echo -e "--- Enabling Elasticsearch ---"
php ${magento_path}/bin/magento -n config:set catalog/search/enable_eav_indexer 1
php ${magento_path}/bin/magento -n config:set catalog/search/engine elasticsearch6
php ${magento_path}/bin/magento -n config:set catalog/search/elasticsearch6_server_hostname elasticsearch
php ${magento_path}/bin/magento -n config:set catalog/search/elasticsearch6_server_port 9200
php ${magento_path}/bin/magento -n config:set catalog/search/elasticsearch6_index_prefix magento2
php ${magento_path}/bin/magento -n config:set catalog/search/elasticsearch6_enable_auth 0
php ${magento_path}/bin/magento -n config:set catalog/search/elasticsearch6_server_timeout 15

# Disable ES6 replication for automated testing
curl -s -XPUT "elasticsearch:9200/_template/default_template" -H 'Content-Type: application/json' -d'{"index_patterns": ["*"],"settings": {"index": {"number_of_replicas": 0}}}'
echo ""

# Enable Varnish
echo -e "--- Enabling Varnish ---"
php ${magento_path}/bin/magento -n config:set --scope=default --scope-code=0 system/full_page_cache/caching_application 2
php ${magento_path}/bin/magento -n config:set system/full_page_cache/varnish/access_list localhost,magento-app,fpm
php ${magento_path}/bin/magento -n config:set system/full_page_cache/varnish/backend_host web
php ${magento_path}/bin/magento -n config:set system/full_page_cache/varnish/backend_port 8080

# Apply configuration changes
echo -e "--- Applying changes to Magento ---"
php ${magento_path}/bin/magento -n setup:upgrade
echo -e "--- Enable production mode in Magento ---"
php ${magento_path}/bin/magento -n deploy:mode:set production
echo -e "--- Flushing Magento Cache ---"
php ${magento_path}/bin/magento -n cache:flush
echo -e "--- Reindexing Magento Catalog ---"
php ${magento_path}/bin/magento -n indexer:reindex catalogsearch_fulltext

# Create Allure folders for MFTF
if [ ! -d /tmp/allure-report ]; then
mkdir /tmp/allure-report
fi
mkdir /tmp/allure-output
