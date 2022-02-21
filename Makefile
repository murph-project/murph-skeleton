COMPOSER_BIN ?= composer
PHP_BIN ?= php8.1
SSH_BIN ?= ssh
YARN_BIN ?= yarn
NPM_BIN ?= npm

all: build

asset-watch:
	$(YARN_BIN)
	$(NPM_BIN) run watch

asset: js-routing
	$(YARN_BIN)
	$(NPM_BIN) run build

js-routing: doctrine-migration
	$(PHP_BIN) bin/console fos:js-routing:dump --format=json --target=public/js/fos_js_routes.json

clean:
	rm -fr var/cache/dev/*
	rm -fr var/cache/prod/*

doctrine-migration:
	PHP=$(PHP_BIN) ./bin/doctrine-migrate

build: clean js-routing asset
