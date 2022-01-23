COMPOSER ?= composer
PHP ?= php8.1
SSH ?= ssh
WEBPACK ?= webpack
YARN ?= yarn

all: dep asset clean

.ONESHELL:
dep:
	$(COMPOSER) update --ignore-platform-reqs
	$(COMPOSER) install --ignore-platform-reqs
	$(YARN)

asset-watch:
	$(WEBPACK) -w

asset: js-routing
	$(YARN)
	$(WEBPACK)

js-routing:
	$(PHP) bin/console fos:js-routing:dump --format=json --target=public/js/fos_js_routes.json

clean:
	rm -fr var/cache/dev/*
	rm -fr var/cache/prod/*

doctrine-migration:
	PHP=$(PHP) ./bin/doctrine-migrate
