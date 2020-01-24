#!/usr/bin/env sh

bin/console doctrine:database:drop --force --no-debug
bin/console doctrine:database:create --no-debug
bin/console doctrine:schema:create --no-debug
bin/console doctrine:fixtures:load --no-interaction --append
