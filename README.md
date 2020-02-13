# m2-local-env


## THIS CODE ORIGINATED FROM magento-mts/mts-local-env

## CodeBuild Notes

The code-coverage-one job:

1. Runs prebuild.sh
2. Runs createMagentoInstall.sh
3. Uploads the resulting `install.tar` to S3 to be used later

The code-coverage-two job:

1. Fetches `install.tar` from S3
2. Runs mftf_single_group.sh
3. Uploads the resulting `.cov` and `allure-report` files to S3
