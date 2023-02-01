# Variables
DOCKER = docker
DOCKER_COMPOSE = docker compose
EXEC = $(DOCKER) exec -w /var/www/ www_sfuploads
PHP = $(EXEC) php
COMPOSER = $(EXEC) composer
NPM = $(EXEC) npm
SYMFONY_CONSOLE = $(PHP) bin/console
SYMFONY = php bin/console
VENDOR = php vendor/bin/

# Colors
GREEN = /bin/echo -e "\x1b[32m\#\# $1\x1b[0m"
RED = /bin/echo -e "\x1b[31m\#\# $1\x1b[0m"

## —— 🔥 App ——
init: ## Init the project
	$(MAKE) start
	$(MAKE) composer-install
	$(MAKE) npm-install
	@$(call GREEN,"The application is available at: http://127.0.0.1:8000/.")
.PHONY: init

debug-router: ## Debug router
	$(SYMFONY_CONSOLE) debug:router
.PHONY: debug-router

cache-clear: ## Clear cache
	$(SYMFONY_CONSOLE) cache:clear
.PHONY: cache-clear

sf: ## Symfony console with cmd argument (make sf cmd="list").
	$(SYMFONY_CONSOLE) $(cmd)
.PHONY: sf

list: ## Make list - sf commands available
	$(SYMFONY_CONSOLE) list
.PHONY: list

entity: ## Make entity
	$(SYMFONY) make:entity
.PHONY: entity

controller: ## Make controller
	$(SYMFONY) make:controller
.PHONY: controller

crud: ## Make crud
	$(SYMFONY) make:crud 
.PHONY: crud

form: ## Make form
	$(SYMFONY) make:form
.PHONY: form

## —— ✅ Test ——

tests: ## Run all tests
	$(MAKE) database-init-test
	$(PHP) bin/phpunit --testdox tests/Unit/
	$(PHP) bin/phpunit --testdox tests/Functional/
	$(PHP) bin/phpunit --testdox tests/E2E/
.PHONY: tests

database-init-test: ## Init database for test
	$(SYMFONY_CONSOLE) d:d:d --force --if-exists --env=test
	$(SYMFONY_CONSOLE) d:d:c --env=test
	$(SYMFONY_CONSOLE) d:m:m --no-interaction --env=test
	$(SYMFONY_CONSOLE) d:f:l --no-interaction --env=test
.PHONY: database-init-test

unit-test: ## Run unit tests
	$(MAKE) database-init-test
	$(PHP) bin/phpunit --testdox tests/Unit/
.PHONY: unit-test

functional-test: ## Run functional tests
	$(MAKE) database-init-test
	$(PHP) bin/phpunit --testdox tests/Functional/
.PHONY: functional-test

# PANTHER_NO_HEADLESS=1 ./bin/phpunit --filter LikeTest --debug to debug with Chrome
e2e-test: ## Run E2E tests
	$(MAKE) database-init-test
	$(PHP) bin/phpunit --testdox tests/E2E/
.PHONY: e2e-test

coverage-test: ## coverage-html var/log/test/test-coverage
	$(MAKE) database-init-test
	$(SYMFONY) bin/phpunit --coverage-html var/log/test/test-coverage
.PHONY: coverage-test

## —— 🐳 Docker ——
start: ## Start app
	$(MAKE) docker-start 
.PHONY: start

docker-start: 
	$(DOCKER_COMPOSE) up -d
.PHONY: docker-start

stop: ## Stop app
	$(MAKE) docker-stop
.PHONY: stop

docker-stop: 
	$(DOCKER_COMPOSE) stop
	@$(call GREEN,"The containers are now stopped.")
.PHONY: docker-stop

prune:
	$(DOCKER) system prune -a
.PHONY: prune

redocker:
	$(MAKE) docker-stop prune start
.PHONY: redocker

container-exec: ## (make container-exec cmd="vendor/bin/bdi detect drivers").
	$(EXEC) $(cmd)
.PHONY: container-exec

## —— 🎻 Composer ——
composer-install: ## Install dependencies
	$(COMPOSER) install
.PHONY: composer-install

composer-update: ## Update dependencies
	$(COMPOSER) update
.PHONY: composer-update

composer-require: ## (make composer-require cmd="--dev symfony/tiers-bundle").
	$(COMPOSER) require $(cmd)
.PHONY: composer-require

composer-require-dev: ## (make composer-require-dev cmd="--dev symfony/tiers-bundle").
	$(COMPOSER) require --dev $(cmd)
.PHONY: composer-require-dev

## —— 🐈 NPM ———————
npm-install: ## Install all npm dependencies
	$(NPM) install
.PHONY: npm-install

npm-update: ## Update all npm dependencies
	$(NPM) update
.PHONY: npm-update

npm-watch: ## Update all npm dependencies
	$(NPM) run watch
.PHONY: npm-watch

## —— 📊 Database ——
database-init: ## Init database
	$(MAKE) database-drop
	$(MAKE) database-create
	$(MAKE) database-migrate
	$(MAKE) database-fixtures-load
.PHONY: database-init

database-drop: ## Create database
	$(SYMFONY_CONSOLE) d:d:d --force --if-exists
.PHONY: database-drop

database-create: ## Create database
	$(SYMFONY_CONSOLE) d:d:c --if-not-exists
.PHONY: database-create

database-remove: ## Drop database
	$(SYMFONY_CONSOLE) d:d:d --force --if-exists
.PHONY: database-remove

database-migration: ## Make migration
	$(SYMFONY_CONSOLE) make:migration
.PHONY: database-migration

migration: ## Alias : database-migration
	$(MAKE) database-migration
.PHONY: migration

database-migrate: ## Migrate migrations
	$(SYMFONY_CONSOLE) d:m:m --no-interaction
.PHONY: database-fixtures-load

migrate: ## Alias : database-migrate
	$(MAKE) database-migrate
.PHONY: database-migrate

database-fixtures-load: ## Load fixtures
	$(SYMFONY_CONSOLE) d:f:l --no-interaction
.PHONY: database-fixtures-load

fixtures: ## Alias : database-fixtures-load
	$(MAKE) database-fixtures-load
.PHONY: fixtures

## -- Validation --
cs-fixer-dry-run: ## Run php-cs-fixer in dry-run mode.
	tools/php-cs-fixer/vendor/bin/php-cs-fixer fix ./src --rules=@Symfony --verbose --dry-run
.PHONY: cs-fixer-dry-run

cs-fixer: ## Run php-cs-fixer.
	tools/php-cs-fixer/vendor/bin/php-cs-fixer fix ./src --rules=@Symfony --verbose
.PHONY: cs-fixer

phpstan: ## Run phpstan.
	php vendor/bin/phpstan analyse ./src --level=7
.PHONY: phpstan

security-checker: ## Run security-checker.
	symfony check:security 
.PHONY: security-checker

lint-twigs: ## Lint twig files.
	$(SYMFONY_CONSOLE) lint:twig ./templates
.PHONY: lint-twigs

lint-yaml: ## Lint yaml files.
	$(SYMFONY_CONSOLE) lint:yaml ./config
.PHONY: lint-yaml

lint-container: ## Lint container.
	$(SYMFONY_CONSOLE) lint:container
.PHONY: lint-container

lint-schema: ## Lint Doctrine schema.
	$(SYMFONY_CONSOLE) doctrine:schema:validate --skip-sync -vvv --no-interaction
.PHONY: lint-schema


before-commit: cs-fixer phpstan lint-twigs lint-yaml lint-container lint-schema security-checker tests ## Run before commit.
.PHONY: before-commit

## —— 🛠️  Others ——
help: ## List of commands
	@grep -E '(^[a-zA-Z0-9_-]+:.*?##.*$$)|(^##)' $(MAKEFILE_LIST) | awk 'BEGIN {FS = ":.*?## "}{printf "\033[32m%-30s\033[0m %s\n", $$1, $$2}' | sed -e 's/\[32m##/[33m/'
.PHONY: help	