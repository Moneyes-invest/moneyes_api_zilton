EXEC_PHP        = symfony php
COMPOSER        = symfony composer

tools/phpmd/composer.lock: tools/phpmd/composer.json
	$(COMPOSER) --working-dir=tools/phpmd update

tools/phpmd/vendor: composer.lock
	$(COMPOSER) --working-dir=tools/phpmd install

.PHONY: phpmd
phpmd: tools/phpmd/vendor
	$(EXEC_PHP) tools/phpmd/vendor/bin/phpmd $(arguments)

.PHONY: apply-phpmd
apply-phpmd:
	$(MAKE) phpmd arguments="src text .phpmd.xml"

.PHONY: update-phpmd
update-phpmd:
	$(COMPOSER) --working-dir=tools/phpmd update

tools/phpcpd/composer.lock: tools/phpcpd/composer.json
	$(COMPOSER) --working-dir=tools/phpcpd update

tools/phpcpd/vendor: composer.lock
	$(COMPOSER) --working-dir=tools/phpcpd install

.PHONY: phpcpd
phpcpd: tools/phpcpd/vendor
	$(EXEC_PHP) tools/phpcpd/vendor/bin/phpcpd $(arguments)

.PHONY: apply-phpcpd
apply-phpcpd:
	$(MAKE) phpcpd arguments="src --exclude src/Report --exclude src/Entity/Contract/ContractBeneficiary.php --exclude src/Broker/Security/StelloSaleBrokerRoleGuesser.php --exclude src/Broker/Security/StelloManagerBrokerRoleGuesser.php --exclude src/Administrator/Security/AdministratorRoleGuesser.php"

.PHONY: update-phpcpd
update-phpcpd:
	$(COMPOSER) --working-dir=tools/phpcpd update

tools/phpstan/composer.lock: tools/phpstan/composer.json
	$(COMPOSER) --working-dir=tools/phpstan update

tools/phpstan/vendor: composer.lock
	$(COMPOSER) --working-dir=tools/phpstan install

.PHONY: phpstan
phpstan: tools/phpstan/vendor
	$(EXEC_PHP) tools/phpstan/vendor/bin/phpstan $(arguments)

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
	$(EXEC_PHP) tools/php-cs-fixer/vendor/bin/php-cs-fixer $(arguments)

.PHONY: check-php-cs
check-php-cs:
	$(MAKE) php-cs-fixer arguments="fix --dry-run --using-cache=no --verbose --diff"

.PHONY: apply-php-cs
apply-php-cs:
	$(MAKE) php-cs-fixer arguments="fix --using-cache=no --verbose --diff"

.PHONY: update-php-cs-fixer
update-php-cs-fixer:
	$(COMPOSER) --working-dir=tools/php-cs-fixer update

pre-commit: apply-phpmd apply-phpcpd apply-php-cs apply-phpstan