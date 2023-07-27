## [Unreleased]

### Fixed
* fix collection widget: allow_add/allow_delete and prototype
### Added
* add user admin controller and simples views in default files
* add chdir in the console entrypoint
### Changed
* upgrade murph/murph-core

## [1.19.0] 2023-04-15
### Changed
* upgrade murph/murph-core

## [1.18.0] 2023-01-13
### Added
* feat(dep): update dependencies
* feat(update): apply new recipe for phpunit
* feat(update): apply recipes:update doctrine/doctrine-bundle
* feat(update): apply recipes:update doctrine/doctrine-migrations-bundle
* feat(update): apply recipes:update liip/imagine-bundle
* feat(update): apply recipes:update stof/doctrine-extensions-bundle
* feat(update): apply recipes:update symfony/apache-pack
* feat(update): apply recipes:update symfony/console
* feat(update): apply recipes:update symfony/debug-bundle
* feat(update): apply recipes:update symfony/flex
* feat(update): apply recipes:update symfony/mailer
* feat(update): apply recipes:update symfony/framework-bundle
* feat(update): apply recipes:update symfony/monolog-bundle
* feat(update): apply recipes:update symfony/routing
* feat(update): apply recipes:update symfony/security-bundle
* feat(update): apply recipes:update symfony/translation
* feat(update): apply recipes:update symfony/twig-bundle
* feat(update): apply recipes:update symfony/validator
* feat(update): apply recipes:update symfony/web-profiler-bundle
* feat(update): apply recipes:update symfony/webpack-encore-bundle
* feat(update): apply recipes:update scheb/2fa-bundle
### Fixed
* fix(config): fix typo in 2fa conf
* fix(config): fix firewall config


## [1.17.0] 2022-11-19
### Changed
* upgrade murph/murph-core
* replace annotation with attributes
* use encore from node_modules in npm scripts

## [1.16.0]
### Added
* add a admin dashboard controller
* add meta description in base.html.twig
### Fixed
### Changed
* upgrade murph/murph-core

## [1.15.0]
### Changed
* upgrade murph/murph-core

## [1.14.3]
### Added
* add blocks in default template
### Changed
* upgrade murph/murph-core

## [1.14.2]

## [1.14.1]
### Fixed
* fix missing envvar in makefile (npm)

## [1.14.0]
### Changed
* upgrade murph/murph-core

## [1.13.0]
### Changed
* upgrade murph/murph-core

## [1.12.0]
### Changed
* upgrade murph/murph-core

## [1.11.0]
### Changed
* upgrade murph/murph-core
* use murph-npm to install npm requirements

## [1.10.0]
### Added
* add translated title in dashboard template
### Fixed
* remove useless env var from makefile
### Changed
* upgrade murph/murph-core

## [1.9.1] - 2022-03-14
### Added
* add murph version in autoload file
### Changed
* remove AdminController constructor

## [1.9.0] - 2022-03-13
### Added
* add murph version in admin ui
### Changed
* the core is now installed with composer

## [1.8.0] - 2022-03-10
### Added
* add security roles in app configuration
* add option to restrict node access to specific roles
### Changed
* rename `core/EventSuscriber` with `core/EventSubscriber`

## [1.7.3] - 2022-03-06
### Added
* add ability to rename file in the file manager
### Fixed
* fix user factory
* fix user creation from ui

## [1.7.2] - 2022-03-03
### Added
* add templates to render sections and items in the admin menu
### Fixed
* fix the analytic table when a path is a long

## [1.7.1] - 2022-03-01
### Added
* add translations
### Fixed
* fix missing directories

## [1.7.0] - 2022-03-01
### Fixed
* fix the analytic referers table when a referer has a long domain
### Changed
* upgrade dependencies
* move assets to the core directory

## [1.6.0] - 2022-02-28
### Added
* add block in field templates to allow override
* merge route params in crud admin redirects
* improve murph:user:create command

### Fixed
* fix form namespace prefix in the crud controller maker
* fix date field when the value is empty
* fix crud batch column width
* fix sidebar icon width
* fix cache clear task

### Changed
* remove password generation from the user factory

## [1.5.0] - 2022-02-25
### Added
* add desktop views and mobile views

### Changed
* upgrade dependencies
* replace jaybizzle/crawler-detect with matomo/device-detector

## [1.4.1] - 2022-02-23
### Added
* handle app urls in twig routing filters

### Fixed
* fix views in analytics modal
* replace empty path with "/" in analytics
### Changed
* update default templates

## [1.4.0] - 2022-02-21
### Added
* add basic analytics

## [1.3.0] - 2022-02-19
### Added
* add support of regexp with substitution in redirect
* url tags can be used as redirect location
* add builders to replace file information tags and url tags

### Fixed
* fix filemanager sorting
* fix batch action setter

## [1.2.0] - 2022-02-14
### Added
* add sort in file manager
* add redirect manager

### Changed
* replace node-sass with sass

## [1.1.0] - 2022-02-29
### Added
* add directory upload in file manager

### Fixed
* fix admin node routing

### Changed
* symfony/swiftmailer-bundle is replaced by symfony/mailer

## [1.0.1] - 2022-02-25
### Fixed
* fix Makefile environment vars (renaming)
* fix composer minimum stability

## [1.0.0] - 2022-01-23
