#!/bin/bash
# Run this before performing docker-compose up to build Magento install.tar file.
# Before building Magento, the html folder should exist with Magento already cloned.
# Specify branch or tag, Github organization, and Github repository below
# set GITHUB_TOKEN environment variable if you need to use a Github token to access the repository
BRANCH_TAG=2.4-develop
ORG=magento
REPO=magento2

if [ -z "$GITHUB_TOKEN" ]; then
    git clone --depth=1 -b ${BRANCH_TAG} https://github.com/${ORG}/${REPO} html
else
    git clone --depth=1 -b ${BRANCH_TAG} https://${GITHUB_TOKEN}:x-oauth-basic@github.com/${ORG}/${REPO} html
fi

# Use custom pcov enabled index.php file
#cp index.php html/pub/index.php
# Copy file responsible for setting correct Test Names
#cp test.php html/pub/test.php
mkdir html/pub/cov

rm -rf html/.git
chown -R 33:33 html
