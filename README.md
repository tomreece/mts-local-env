# m2-local-env

These are the steps needed to run Magento in an 8 container configuration using Magento Docker Cloud images.

These steps were ran on an Ubuntu 18.04 VirtualBox VM `ubuntu/bionic64` and EC2 instance

## MFTF by Parallel group

1. Run `apt-get update; apt-get -y install docker.io python-pip openjdk-8-jre-headless zip unzip git; pip install docker-compose`
2. Clone this repository and run the steps below inside of the m2-local-env folder
3. Run `read -s GITHUB_TOKEN` to supply your Github API token (if needed). Then run `export GITHUB_TOKEN`.
4. Update `prebuild.sh` with specific Org, Branch, and Repo information (if needed)
5. Run `bash prebuild.sh` to clone magento2ce repository 
6. Run `bash createMagentoInstall.sh` to install Magento and build **install.tar**
7. Run `bash mftf_by_group.sh` to start MFTF group tests. Note this script can be updated to limit testing to less groups
8. Run `bash createAllureReport.sh` to generate the allure report

## MFTF by individual test

1. Run `apt-get update; apt-get -y install docker.io python-pip openjdk-8-jre-headless zip unzip git; pip install docker-compose`
2. Clone this repository and run the steps below inside of the m2-local-env folder
3. Run `read -s GITHUB_TOKEN` to supply your Github API token (if needed). Then run `export GITHUB_TOKEN`.
4. Update `prebuild.sh` with specific Org, Branch, and Repo information (if needed)
5. Run `bash prebuild.sh` to clone magento2ce repository
6. Run `bash createMagentoInstall.sh` to install Magento and build **install.tar**
7. Update mftf-test-list.txt with list of MFTF test names
8. Run `bash mftf_by_test.sh` to start MFTF individual tests
9. Run `bash createAllureReport.sh` to generate the allure report

## Unit Tests

1. Run `apt-get update; apt-get -y install docker.io python-pip openjdk-8-jre-headless zip unzip git; pip install docker-compose`
2. Clone this repository and run the steps below inside of the m2-local-env folder
3. Run `read -s GITHUB_TOKEN` to supply your Github API token (if needed). Then run `export GITHUB_TOKEN`.
4. Update `prebuild.sh` with specific Org, Branch, and Repo information (if needed)
5. Run `bash prebuild.sh` to clone magento2ce repository
6. Run `bash createMagentoInstall.sh` to install Magento and build **install.tar**
7. Run `bash unit_test.sh` to generate unit test reports in allure-report folder

## JSUnit Tests

1. Run `apt-get update; apt-get -y install docker.io python-pip openjdk-8-jre-headless zip unzip git; pip install docker-compose`
2. Clone this repository and run the steps below inside of the m2-local-env folder
3. Run `read -s GITHUB_TOKEN` to supply your Github API token (if needed). Then run `export GITHUB_TOKEN`.
4. Update `prebuild.sh` with specific Org, Branch, and Repo information (if needed)
5. Run `bash prebuild.sh` to clone magento2ce repository
6. Run `bash createMagentoInstall.sh` to install Magento and build **install.tar**
7. Run `bash jsunit_test.sh` to generate unit test reports in allure-report folder

## Running Magento installation without tests

1. Run `apt-get update; apt-get -y install docker.io python-pip openjdk-8-jre-headless zip unzip git; pip install docker-compose`
2. Clone this repository and run the steps below inside of the m2-local-env folder
3. Run `read -s GITHUB_TOKEN` to supply your Github API token (if needed). Then run `export GITHUB_TOKEN`.
4. Update `prebuild.sh` with specific Org, Branch, and Repo information (if needed)
5. Run `bash prebuild.sh` to clone magento2ce repository (update BRANCH_TAG if needed)
6. Run `bash createMagentoInstall.sh` to install Magento and build **install.tar**
7. Run `bash run_magento.sh start` to start all of the containers
8. Add `IP_ADDRESS magento.local` to your hosts file
9. Browse to `http://magento.local` to access Magento

_**NOTE:** When using Vagrant, use 127.0.0.1 for IP\_ADDRESS and add the following line to Vagrant config_<br/>
`config.vm.network "forwarded_port", guest: 80, host: 80`


##  Sample Vagrantfile

```
# -*- mode: ruby -*-
# vi: set ft=ruby :


# 1.  Clone https://git.corp.adobe.com/magento-mts/m2-local-env to c:/vagrant/m2-local-env

Vagrant.configure("2") do |config|
   config.vm.box = "ubuntu/bionic64"
   config.vm.provision "shell", inline: <<-SHELL
   sysctl -w vm.max_map_count=262144
   apt-get -y update
   apt-get -y install docker.io python-pip openjdk-8-jre-headless zip unzip git
   pip install docker-compose
   cp -rp /vagrant/m2-local-env /tmp
   cd /tmp/m2-local-env
   chmod go-w server.cnf
   bash prebuild.sh
   bash createMagentoInstall.sh
   # Only run group 1 instead of all groups
   sed -i 's/^END_GROUP=.*/END_GROUP=1/g' mftf_by_group.sh
   bash mftf_by_group.sh
   bash mftf_by_test.sh
   bash createAllureReport.sh
   bash unit_test.sh
   bash jsunit_test.sh
   SHELL
  config.vm.provider "virtualbox" do |v|
  v.memory = 4096
  v.cpus = 4
end
end
```
