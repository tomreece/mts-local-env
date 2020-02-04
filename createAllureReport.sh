#!/bin/bash
# Generates Allure Report in allure-output
ALLURE_VERSION="2.13.0"

if [ ! -f allure-commandline-${ALLURE_VERSION}.zip ]; then
  echo -e '--- Downloading Allure ---'
  wget -q https://dl.bintray.com/qameta/maven/io/qameta/allure/allure-commandline/${ALLURE_VERSION}/allure-commandline-${ALLURE_VERSION}.zip -O allure-commandline-${ALLURE_VERSION}.zip
fi

if [ ! -d allure-${ALLURE_VERSION} ]; then
  echo -e '--- Installing Allure ---'
  unzip -q allure-commandline-${ALLURE_VERSION}.zip
fi

if [ -f allure-${ALLURE_VERSION}/bin/allure ]; then
 echo -e '--- Generating Allure Report ---'
 ./allure-${ALLURE_VERSION}/bin/allure generate allure-report -c -o allure-output
fi
