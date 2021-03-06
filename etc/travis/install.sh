#!/usr/bin/env bash
set -e

source "$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)/lib/common.sh"

code=0
BUILD_DIR=$PWD
SITE_DOMAIN=siap.localhost

if [[ $API = yes || $COVERAGE = yes || $INTEGRATION = yes ]]; then
  cp etc/travis/config/env.local api/.env.local
  cd ${BUILD_DIR}/api
  run_command "composer install --ansi"
  run_command "composer prepare-test --ansi"
fi;

if [[ $API != yes ]]; then
  run_command "cd ${BUILD_DIR}/client"
  yarn;
fi;

if [[ $INTEGRATION = yes ]]; then
  run_command "cp ${BUILD_DIR}/etc/travis/config/client.local ${BUILD_DIR}/client/.env.local"
fi;

if [[ $DEPLOY = yes || $INTEGRATION = yes ]]; then
  run_command "cd ${BUILD_DIR}/client"
  yarn build;
fi;

if [[ $INTEGRATION = yes ]]; then
 run_command "cd ${BUILD_DIR}"
 run_command "chmod 775 -Rvf client/dist"
 run_command "sudo gpasswd -a www-data travis"
 run_command "sudo chown travis:www-data -R client/dist"
 run_command "php-fpm --fpm-config ~/.phpenv/versions/$(phpenv version-name)/etc/php-fpm.conf.default --define cgi.fix_pathinfo=0 -i | grep cgi.fix_pathinfo"
 run_command "php-fpm --fpm-config ~/.phpenv/versions/$(phpenv version-name)/etc/php-fpm.conf.default --define cgi.fix_pathinfo=0 && ps -C php-fpm"
 run_command "sudo cp etc/travis/config/nginx.conf /etc/nginx/sites-available/$SITE_DOMAIN"
 run_command 'sudo sed -e "s?%TRAVIS_BUILD_DIR%?$TRAVIS_BUILD_DIR?g" --in-place /etc/nginx/sites-available/$SITE_DOMAIN'
 run_command 'sudo sed -e "s?%SITE_DOMAIN%?$SITE_DOMAIN?g" --in-place /etc/nginx/sites-available/$SITE_DOMAIN'
 run_command 'sudo ln -s /etc/nginx/sites-available/$SITE_DOMAIN /etc/nginx/sites-enabled/'
 run_command "sudo service nginx status"
 run_command 'sudo service nginx restart'
fi;

exit ${code}
