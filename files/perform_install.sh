#!/bin/bash
export TERM='dumb'
magento_path='/app'
php -v
cd ${magento_path} || exit

curl -s 'https://getcomposer.org/download/1.9.1/composer.phar' -o /usr/bin/composer
chmod 0755 /usr/bin/composer

find ${magento_path}/dev ${magento_path}/var ${magento_path}/generated ${magento_path}/pub/static ${magento_path}/vendor ${magento_path}/pub/media ${magento_path}/app/etc -type f -exec chmod ug+w {} \; || true
find ${magento_path}/dev ${magento_path}/var ${magento_path}/generated ${magento_path}/pub/static ${magento_path}/vendor ${magento_path}/pub/media ${magento_path}/app/etc -type d -exec chmod ug+ws {} \; || true

# Install Magento
cd ${magento_path} || exit; php /usr/bin/composer install

# pcov clobber
php /usr/bin/composer require pcov/clobber
${magento_path}/vendor/bin/pcov clobber

php bin/magento -n setup:install \
      --base-url="http://magento.local" \
      --backend-frontname="admin" \
      --db-host="${MARIADB_HOST}" \
      --db-name="${MAGENTO_DATABASE_NAME}" \
      --db-user="${MAGENTO_DATABASE_USER}" \
      --db-password="${MAGENTO_DATABASE_PASSWORD}" \
      --admin-user="admin" \
      --admin-password="123123q" \
      --admin-firstname="admin" \
      --admin-lastname="lastname" \
      --admin-use-security-key="0" \
      --admin-email="admin@example.com" \
      --language="en_US" \
      --currency="USD" \
      --timezone="UTC" \
      --use-rewrites=1 \
      --use-secure=0 \
      --use-secure-admin=0
php bin/magento -n deploy:mode:set production
php bin/magento -n maintenance:disable
php bin/magento -n config:set admin/security/admin_account_sharing 1
php bin/magento -n indexer:reindex
cd ${magento_path}/dev/tests/acceptance || exit
# build MFTF parallel groups
echo "--- Build Project ---"
${magento_path}/vendor/bin/mftf build:project --MAGENTO_BASE_URL http://magento.local/ 
echo "--- Generate Tests ---"
${magento_path}/vendor/bin/mftf generate:tests --config parallel
# configure MFTF
sed -i "s#%MAGENTO_BASE_URL%#http://magento.local/#g" ${magento_path}/dev/tests/acceptance/tests/functional.suite.yml
sed -i "s#%MAGENTO_BACKEND_BASE_URL%#http://magento.local/#g" ${magento_path}/dev/tests/acceptance/tests/functional.suite.yml
sed -i "s#%MAGENTO_ADMIN_USERNAME%#admin#g" ${magento_path}/dev/tests/acceptance/tests/functional.suite.yml
sed -i "s#%MAGENTO_ADMIN_PASSWORD%#123123q#g" ${magento_path}/dev/tests/acceptance/tests/functional.suite.yml
sed -i "s#%MAGENTO_BACKEND_NAME%#admin#g" ${magento_path}/dev/tests/acceptance/tests/functional.suite.yml
sed -i "s#%SELENIUM_HOST%#selenium#g" ${magento_path}/dev/tests/acceptance/tests/functional.suite.yml
sed -i "s#%SELENIUM_PORT%#4444#g" ${magento_path}/dev/tests/acceptance/tests/functional.suite.yml
sed -i "s#%SELENIUM_PROTOCOL%#http#g" ${magento_path}/dev/tests/acceptance/tests/functional.suite.yml
sed -i "s#%SELENIUM_PATH%#/wd/hub#g" ${magento_path}/dev/tests/acceptance/tests/functional.suite.yml
sed -i 's#--enable-Passthrough#--no-sandbox", "--headless#g' ${magento_path}/dev/tests/acceptance/tests/functional.suite.yml
sed -i "s#MAGENTO_BASE_URL=.*#MAGENTO_BASE_URL=http://magento.local/#g" ${magento_path}/dev/tests/acceptance/.env; 
rm -f ${magento_path}/vendor/magento/magento2-functional-testing-framework/etc/_envs/*.yml
chown -R www-data:www-data ${magento_path}/
# flush Magento cache
${magento_path}/bin/magento -n cache:flush

