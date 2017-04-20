#!/bin/bash
#
# --------------------------------------------------------------------
# This script installs Composer (https://getcomposer.org/).
#
# Requirements:
#   - Unix operating system
# --------------------------------------------------------------------

PWD=$(cd `dirname $0` && pwd)

COMPOSER_VERSION="1.4.1"

pushd . > /dev/null 2>&1
cd "$SCRIPT_DIR"
  ##
  # Get installer

  EXPECTED_SIGNATURE=$(wget -q -O - https://composer.github.io/installer.sig)
  php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
  ACTUAL_SIGNATURE=$(php -r "echo hash_file('SHA384', 'composer-setup.php');")

  if [ "$EXPECTED_SIGNATURE" != "$ACTUAL_SIGNATURE" ]; then
    echo 'ERROR: Invalid installer signature' >&2
    rm "composer-setup.php"
    popd > /dev/null 2>&1
    exit 1
  fi

  php "composer-setup.php" --version "$COMPOSER_VERSION" --quiet
  if [ $? -ne 0 ]; then
    echo 'ERROR: Setup command has failed' >&2
    rm "composer-setup.php"
    popd > /dev/null 2>&1
    exit 1
  fi

  rm "composer-setup.php"

  ##
  # Install Composer

  php composer.phar install
  if [ $? -ne 0 ]; then
    echo 'ERROR: Install command has failed' >&2
    popd > /dev/null 2>&1
    exit 1
  fi

  echo "DONE!"
popd > /dev/null 2>&1

# EOF

