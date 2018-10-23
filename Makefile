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
	docker-compose -f docker-compose.test.yaml run --rm test ./vendor/bin/phpunit
	docker-compose down
.PHONY: test

phpstan: ## Checks phpstan
	docker-compose -f docker-compose.test.yaml run --rm phpstan
.PHONY: phpstan

cs-fix: ## Fixes code standards
	docker-compose -f docker-compose.test.yaml run --rm test ./vendor/bin/php-cs-fixer fix --verbose --diff
.PHONY: cs-fix

cs-check: ## Checks code standards
	docker-compose -f docker-compose.test.yaml run --rm test ./vendor/bin/php-cs-fixer fix -vvv --diff --dry-run
.PHONY: cs-check
