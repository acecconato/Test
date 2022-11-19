regenerate: # Generate/Regenerate dev database and fixtures
	php bin/console d:d:d --force
	php bin/console d:d:c
	php bin/console d:m:m
	@echo "Do you want to load default fixtures?"
	@php bin/console h:f:l
.PHONY: regenerate

regenerate-test: # Generate/Regenerate test database and fixtures
	php bin/console d:d:d --force --env=test
	php bin/console d:d:c --env=test
	php bin/console d:m:m --env=test
	php bin/console h:f:l --env=test
.PHONY: regenerate-test

run: # Run dev server
	symfony serve -d
	php bin/console messenger:consume async
.PHONY: run

stop: # Stop dev server
	symfony local:server:stop
	php bin/console messenger:stop-worker
.PHONY: stop

qa: fix phpstan
.PHONY: qa

phpstan: # Execute phpstan
	php vendor/bin/phpstan analyse -c phpstan.neon
.PHONY: phpstan

fix:
	php vendor/bin/php-cs-fixer fix
.PHONY: fix
