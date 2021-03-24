COMPOSER ?= composer
PHP ?= php7.4
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

asset:
	$(YARN)
	$(WEBPACK)

clean:
	rm -fr var/cache/dev/*
	rm -fr var/cache/prod/*

doctrine-migration:
	PHP=$(PHP) ./bin/doctrine-migrate
