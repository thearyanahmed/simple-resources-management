#!/bin/sh
set -e

docker run --rm --interactive --tty --volume $(pwd)/api:/app --volume ${COMPOSER_HOME:-$HOME/.composer}:/tmp composer install

