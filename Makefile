DOCKER_COMPOSE	= docker compose

EXEC_APP        = docker exec php
EXEC_PG         = docker exec database
EXEC_PG_I       = docker exec -i database
SYMFONY         = $(EXEC_APP) bin/console
COMPOSER        = $(EXEC_APP) composer

##
## Project
## -------
##

build:
	$(DOCKER_COMPOSE) pull --parallel --ignore-pull-failures
	$(DOCKER_COMPOSE) build --pull

kill:
	-@$(DOCKER_COMPOSE) kill 2> /dev/null
	$(DOCKER_COMPOSE) down --volumes --remove-orphans

install: ## Install and start the project
install: build start
reset: ## Stop and start a fresh install of the project
reset: kill install

start: ## Start the project
	$(DOCKER_COMPOSE) up -d --remove-orphans

stop:
	$(DOCKER_COMPOSE) stop

connect: start
	$(DOCKER_COMPOSE) exec php sh

clean: ## Stop the project and remove generated files
clean: kill
	rm -rf vendor

clear-cache: ## Clear cache data
	-@$(SYMFONY) cache:clear


log: start
	$(DOCKER_COMPOSE) logs -f

.PHONY: restart-php
restart-php:
	$(DOCKER_COMPOSE) restart php

.PHONY: enable-xdebug
enable-xdebug:
	$(EXEC_APP) sed -i '/xdebug.start_with_request.+/d' /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini
	$(EXEC_APP) sh -c "echo \"xdebug.start_with_request = yes\" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini"

.PHONY: disable-xdebug
disable-xdebug:
	$(EXEC_APP) sed -i '/xdebug.start_with_request.+/d' /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini
	$(EXEC_APP) sh -c "echo \"xdebug.start_with_request = no\" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini"

.PHONY: build kill reset start stop clean clear-cache enable-xdebug disable-xdebug

##
## Utils
## -----
##

db: ## Reset the database and load fixtures
db: vendor
	@$(EXEC_APP) php -r 'echo "Wait database...\n"; set_time_limit(30); require __DIR__."/vendor/autoload.php"; (new \Symfony\Component\Dotenv\Dotenv())->usePutenv(true)->bootEnv(__DIR__."/.env") ;$$u=parse_url(getenv("DATABASE_URL"));set_time_limit(60);for(;;){if(@fsockopen($$u["host"],$$u["port"])){break;}echo "Waiting for database\n";sleep(1);}'
	$(SYMFONY) doctrine:database:drop --force || true
	$(SYMFONY) doctrine:database:create
	$(SYMFONY) doctrine:migrations:migrate --no-interaction --allow-no-migration
	$(SYMFONY) doctrine:fixtures:load --group=dev --no-interaction
	$(SYMFONY) lexik:jwt:generate-keypair --overwrite

migration: ## Generate a new doctrine migration
migration: vendor
	$(SYMFONY) doctrine:migrations:diff

generate-migration: ## Generate a new empty migration
generate-migration: vendor
	$(SYMFONY) doctrine:migrations:generate

apply-migration: ## Run doctrine migration
apply-migration: vendor
	$(SYMFONY) doctrine:migrations:migrate --no-interaction --allow-no-migration

db-validate-schema: ## Validate the doctrine ORM mapping
db-validate-schema: vendor
	$(SYMFONY) doctrine:schema:validate

composer.lock: composer.json
	$(COMPOSER) update --lock --no-scripts --no-interaction

vendor: composer.lock
	$(COMPOSER) install

sync-recipes: vendor
	$(COMPOSER) sync-recipes --force

.PHONY: db migration assets

##
## Quality assurance
## -----------------
##

lint: ## Lints twig and yaml files
lint: lt ly

lt: vendor
	$(SYMFONY) lint:twig templates

ly: vendor
	$(SYMFONY) lint:yaml config

db-test: vendor
	@$(EXEC_APP) php -r 'echo "Wait database...\n"; set_time_limit(30); require __DIR__."/vendor/autoload.php"; (new \Symfony\Component\Dotenv\Dotenv())->usePutenv(true)->bootEnv(__DIR__."/.env.test") ;$$u=parse_url(getenv("DATABASE_URL"));set_time_limit(60);for(;;){if(@fsockopen($$u["host"],$$u["port"])){break;}echo "Waiting for database\n";sleep(1);}'
	$(SYMFONY) doctrine:database:drop --force --env test || true
	$(SYMFONY) doctrine:database:create --env test
	$(SYMFONY) doctrine:migrations:migrate --no-interaction --allow-no-migration --env test

test-fixtures: db-test
	$(SYMFONY) doctrine:fixtures:load --env test --group=dev --append

test: test-fixtures assets
	$(EXEC_APP) php ./vendor/bin/simple-phpunit --testdox

tools:
	mkdir $@

tools/local-php-security-checker-%: tools
	$(EXEC_APP) sh -c "rm -f tools/local-php-security-checker-*"
	$(EXEC_APP) sh -c "curl -LSso $@ \"https://github.com/fabpot/local-php-security-checker/releases/download/v$*/local-php-security-checker_$*_linux_amd64\""
	$(EXEC_APP) sh -c "chmod +x tools/local-php-security-checker-*"

.PHONY: local-php-security-checker
local-php-security-checker: tools/local-php-security-checker-2.0.3
	$(EXEC_APP) $< $(arguments)

.PHONY: security
security:
	$(MAKE) local-php-security-checker arguments=""

tools/phpmd/composer.lock: tools/phpmd/composer.json
	$(COMPOSER) --working-dir=tools/phpmd update

tools/phpmd/vendor: composer.lock
	$(COMPOSER) --working-dir=tools/phpmd install

.PHONY: phpmd
phpmd: tools/phpmd/vendor
	$(EXEC_APP) php tools/phpmd/vendor/bin/phpmd $(arguments)

.PHONY: apply-phpmd
apply-phpmd:
	$(MAKE) phpmd arguments="src text .phpmd.xml"

.PHONY: update-phpmd
update-phpmd:
	$(COMPOSER) --working-dir=tools/phpmd update

tools/phpcpd/composer.lock: tools/phpcpd/composer.jsons
	$(COMPOSER) --working-dir=tools/phpcpd update

tools/phpcpd/vendor: composer.lock
	$(COMPOSER) --working-dir=tools/phpcpd install

.PHONY: phpcpd
phpcpd: tools/phpcpd/vendor
	$(EXEC_APP) php tools/phpcpd/vendor/bin/phpcpd $(arguments)

.PHONY: apply-phpcpd
apply-phpcpd:
	$(MAKE) phpcpd arguments="src"

.PHONY: update-phpcpd
update-phpcpd:
	$(COMPOSER) --working-dir=tools/phpcpd update

tools/phpstan/composer.lock: tools/phpstan/composer.json
	$(COMPOSER) --working-dir=tools/phpstan update

tools/phpstan/vendor: composer.lock
	$(COMPOSER) --working-dir=tools/phpstan install

.PHONY: phpstan
phpstan: tools/phpstan/vendor
	$(EXEC_APP) php tools/phpstan/vendor/bin/phpstan $(arguments)

.PHONY: apply-phpstan
apply-phpstan:
	$(MAKE) phpstan arguments="analyse --memory-limit=4G -l 9 -c .phpstan.neon src"

.PHONY: update-phpstan
update-phpstan:
	$(COMPOSER) --working-dir=tools/phpstan update

tools/php-cs-fixer/composer.lock: tools/php-cs-fixer/composer.json
	$(COMPOSER) --working-dir=tools/php-cs-fixer update

tools/php-cs-fixer/vendor: composer.lock
	$(COMPOSER) --working-dir=tools/php-cs-fixer install

.PHONY: php-cs-fixer
php-cs-fixer: tools/php-cs-fixer/vendor
	$(EXEC_APP) php tools/php-cs-fixer/vendor/bin/php-cs-fixer $(arguments)

.PHONY: check-php-cs
check-php-cs:
	$(MAKE) php-cs-fixer arguments="fix --dry-run --using-cache=no --verbose --diff"

.PHONY: apply-php-cs
apply-php-cs:
	$(MAKE) php-cs-fixer arguments="fix --using-cache=no --verbose --diff"

.PHONY: update-php-cs-fixer
update-php-cs-fixer:
	$(COMPOSER) --working-dir=tools/php-cs-fixer update

update-tools: update-php-cs-fixer update-phpstan update-phpcpd update-phpmd

pre-commit: apply-phpmd apply-phpcpd apply-php-cs apply-phpstan

load-fixtures-prod:
	$(SYMFONY) doctrine:fixtures:load --group=prod --no-interaction

load-fixtures-dev:
	$(SYMFONY) doctrine:fixtures:load --group=dev --no-interaction