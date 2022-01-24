COMPOSER_BIN ?= composer
PHP_BIN ?= php8.1
SSH_BIN ?= ssh
WEBPACK_BIN ?= webpack
YARN_BIN ?= yarn

all: dep asset clean

.ONESHELL:
dep:
	$(COMPOSER_BIN) update --ignore-platform-reqs
	$(COMPOSER_BIN) install --ignore-platform-reqs
	$(YARN_BIN)

asset-watch:
	$(WEBPACK_BIN) -w

asset: js-routing
	$(YARN_BIN)
	$(WEBPACK_BIN)

js-routing:
	$(PHP_BIN) bin/console fos:js-routing:dump --format=json --target=public/js/fos_js_routes.json

clean:
	rm -fr var/cache/dev/*
	rm -fr var/cache/prod/*

doctrine-migration:
	PHP=$(PHP_BIN) ./bin/doctrine-migrate
