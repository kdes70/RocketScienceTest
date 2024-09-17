#!/usr/bin/make

SHELL = /bin/sh

export PWD := $(PWD)

ifneq (,$(wildcard ./.env))
    include ./.env
    export
endif

API_DIR := /

sail_bin := $(shell command -v $(API_DIR)vendor/bin/sail 2> /dev/null)
docker_bin := $(shell command -v docker 2> /dev/null)
code_fix := $(shell command -v  $(API_DIR)vendor/bin/pint  2> /dev/null)

# The "new" version integrates compose in the docker command.
COMPOSE_COMMAND=docker compose

DOCKER_COMPOSE_NEW := $(shell docker compose version 2>/dev/null)
ifndef DOCKER_COMPOSE_NEW
	DOCKER_COMPOSE_OLD := $(shell docker-compose --version 2>/dev/null)
	ifdef DOCKER_COMPOSE_OLD
		COMPOSE_COMMAND = docker-compose
	else
		$(error "docker compose is not available, please install it")
	endif
endif

APP_CONTAINER_NAME := $(APP_SERVICE)

install: down x-perm x-copy-env ## build and install application
	$(sail_bin) build --no-cache
	make up
	make x-perm
	make x-key-generate

init: up x-clear-generated x-clear-cache x-composer-install x-database x-ide ## start application, refresh configs and helpers

up: ## start application containers
	$(sail_bin) up -d

stop: ## stop application containers
	$(sail_bin) stop

down: ## stop and clear application containers
	$(COMPOSE_COMMAND) down -v --remove-orphans

test: up x-test ## clear config and run laravel tests

# Utils: SHELLS
shell: up ## start shell into application container
	$(sail_bin) shell

php-cli: up ## start shell of laravel tinker
	$(sail_bin) tinker

redis-cli: up ## start shell of redis
	$(COMPOSE_COMMAND) exec $(REDIS_CONTAINER_NAME) sh -c redis-cli

review:
	$(COMPOSE_COMMAND) exec $(APP_CONTAINER_NAME) sh -c "/var/www/html/vendor/bin/pint"


# DATABASE
x-database: ## recreating database with all migrations and seeds
	sleep 5; # waiting for mysql initialization (0_o)'
	$(sail_bin) artisan migrate:fresh --seed

x-migrate:
	sleep 5; # waiting for mysql initialization (0_o)'
	$(sail_bin) artisan migrate

# HELPERS
x-copy-env:
	if [ ! -f $(API_DIR)/.env ]; then cp $(API_DIR)/.env.example $(API_DIR)/.env ; fi

x-key-generate:
	$(sail_bin) artisan key:generate

x-ide:
	$(sail_bin) artisan ide-helper:meta
	$(sail_bin) artisan ide-helper:generate
	$(sail_bin) artisan ide-helper:models -N --write-mixin --no-interaction # --write-mixin --no-interaction fix multiple definitions exist for classes in Laravel Ide

x-clear-generated:
	test -f $(API_DIR)/.phpstorm.meta.php && rm $(API_DIR)/.phpstorm.meta.php || true
	test -f $(API_DIR)/_ide_helper.php && rm $(API_DIR)/_ide_helper.php || true
	test -f $(API_DIR)/_ide_helper_models.php && rm $(API_DIR)/_ide_helper_models.php || true
	test -f $(API_DIR)/.phpunit.result.cache && rm $(API_DIR)/.phpunit.result.cache || true
	test -d $(API_DIR)/storage/logs && find $(API_DIR)/storage/logs -name '*.log' -exec rm {} \;

x-clear-cache:
	$(sail_bin) artisan cache:clear

x-perm:
	sudo chown ${USER}:${USER} $(API_DIR)/storage -R
	sudo chgrp -R www-data $(API_DIR)/storage
	sudo chmod -R ug+rwx $(API_DIR)/storage

x-test:
	$(sail_bin) test --env=testing --testsuite=Feature --stop-on-failure

# COMPOSER
x-composer-install:
	$(sail_bin) composer install


