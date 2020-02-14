#!/bin/bash

magento_path='/app'

cd ${magento_path}/pub/cov

# Download phpcov
wget https://phar.phpunit.de/phpcov-6.0.1.phar -O phpcov.phar
chmod +x phpcov.phar

# Merge all .cov files into one
php phpcov.phar merge . --php=group${GROUP_NUMBER}.cov

# Remove all other files so that they are not stashed in S3
ls | grep -v group${GROUP_NUMBER}.cov | xargs rm
