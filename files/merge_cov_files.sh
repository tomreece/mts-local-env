#!/bin/bash

magento_path='/app'

cd ${magento_path}/pub/cov

wget https://phar.phpunit.de/phpcov-6.0.1.phar -O phpcov.phar
chmod +x phpcov.phar

php phpcov.phar merge . --php=group${GROUP_NUMBER}.cov