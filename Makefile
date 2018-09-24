help: ## Show this help message.
	@echo 'usage: make [target] ...'
	@echo
	@echo 'targets:'
	@egrep '^(.+)\:(.+)?\ ##\ (.+)' ${MAKEFILE_LIST} | column -t -c 2 -s ':#'
.PHONY: help

up: ## Ensures prerequisites
	docker-compose up -d rabbitmq

install: ## Installs dependencies
	docker-compose run --rm php composer install --optimize-autoloader --no-interaction
.PHONY: install

test: up ## Runs test suite
	docker-compose run --rm php ./vendor/bin/phpunit
	docker-compose down
.PHONY: test

cs-fix: ## Fixes code standards
	docker-compose run --rm php ./vendor/bin/php-cs-fixer fix --verbose --diff
.PHONY: cs-fix

cs-check: ## Checks code standards
	docker-compose run --rm php ./vendor/bin/php-cs-fixer fix -vvv --diff --dry-run
.PHONY: cs-check
