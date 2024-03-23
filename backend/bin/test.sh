#!/usr/bin/env bash

# Source env file
if [ -f .env.test ]
then
	source .env.test
	export $(grep=.env.test | grep -v '\s*#' | cut -d= -f1)
fi

# Initializing folders
mkdir -p build/logs build/test build/report build/allure

set -e
PHP=php
command -v php8.3 && PHP=$(command -v php8.3) || true

# Dump checker
echo -e "\e[0Ksection_start:`date +%s`:dump_checker_section[collapsed=true]\r\e[0K\e[34mDump checker\e[0m"
${PHP} vendor/bin/var-dump-check --symfony src
echo -e "\e[0Ksection_end:`date +%s`:dump_checker_section\r\e[0K"

set +e

# PHP CodeSniffer
echo -e "\e[0Ksection_start:`date +%s`:phpcs_section[collapsed=true]\r\e[0K\e[34mPHP CodeSniffer\e[0m"
${PHP} vendor/squizlabs/php_codesniffer/bin/phpcs --config-set installed_paths vendor/escapestudios/symfony2-coding-standard
${PHP} vendor/squizlabs/php_codesniffer/bin/phpcs --config-set default_standard Symfony
${PHP} vendor/squizlabs/php_codesniffer/bin/phpcs --extensions=php --report=junit --report-file=build/test/checkstyle-php.xml src
echo -e "\e[0Ksection_end:`date +%s`:phpcpd_section\r\e[0K"

# PHP Mess Detector
echo -e "\e[0Ksection_start:`date +%s`:phpmd_section[collapsed=true]\r\e[0K\e[34mPHP Mess Detector\e[0m"
${PHP} vendor/phpmd/phpmd/src/bin/phpmd src xml config/test/mess.xml --reportfile build/test/mess.xml
echo -e "\e[0Ksection_end:`date +%s`:phpmd_section\r\e[0K"

# PHP Depend
echo -e "\e[0Ksection_start:`date +%s`:pdepend_section[collapsed=true]\r\e[0K\e[34mPHP Depend\e[0m"
${PHP} vendor/pdepend/pdepend/src/bin/pdepend --jdepend-xml=build/test/dependencies.xml --jdepend-chart=build/report/dependencies.svg --overview-pyramid=build/report/dependencies-pyramid.svg src
echo -e "\e[0Ksection_end:`date +%s`:pdepend_section\r\e[0K"

# PHPStan
echo -e "\e[0Ksection_start:`date +%s`:phpstan_section[collapsed=true]\r\e[0K\e[34mPHPStan\e[0m"
${PHP} vendor/bin/phpstan analyse --no-progress --memory-limit=-1
echo -e "\e[0Ksection_end:`date +%s`:phpstan_section\r\e[0K"

# Load fixtures
echo -e "\e[0Ksection_start:`date +%s`:fixtures_section[collapsed=true]\r\e[0K\e[34mLoad fixtures\e[0m"
set -e
${PHP} bin/console doctrine:database:drop --force --env=test
${PHP} bin/console doctrine:database:create --env=test
${PHP} bin/console doctrine:schema:update --complete --force --env=test
${PHP} bin/console cache:clear --env=test
${PHP} -d memory_limit=-1 bin/console doctrine:fixtures:Load --group=test --no-interaction --env=test
echo -e "\e[0Ksection_end:`date +%s`:fixtures_section\r\e[0K"

# Validators
echo -e "\e[0Ksection_start:`date +%s`:validators_section[collapsed=true]\r\e[0K\e[34mLaunch validators (DI, Yaml, etc.)\e[0m"
${PHP} bin/console cache:clear --env=prod
${PHP} bin/console doctrine:migration:migrate --no-interaction --env=prod
${PHP} bin/console lint:yaml config --env=prod
${PHP} bin/console lint:yaml translations --env=prod
${PHP} bin/console lint:container --env=prod
echo -e "\e[0Ksection_end:`date +%s`:validators_section\r\e[0K"

# PHPUnit
echo -e "\e[0Ksection_start:`date +%s`:phpunit_section[collapsed=true]\r\e[0K\e[34mRun PHPUnit\e[0m"
${PHP} -d memory_limit=-1 vendor/phpunit/phpunit/phpunit --log-junit build/test/xunit.xml
echo -e "\e[0Ksection_end:`date +%s`:phpunit_section\r\e[0K"
