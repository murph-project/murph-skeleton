## [Unreleased]

## Upgrade to v1.8.0
### Commands

```
make doctrine-migration
```

### Files

Event subscribers in `src/EventSubscriber` must update namespaces.

## Upgrade to v1.7.0
### Commands

```
yarn add sortablejs@^1.14.0

```

### Files

* `assets/css/_admin_extend.scss` is removed
* `assets/css/_admin_vars.scss` is removed
* `assets/css/_admin_vars.scss` is changed
* `assets/js/admin` is removed
* `assets/js/admin.js` is changed


## Upgrade to v1.5.0
### Commands

```
composer remove jaybizzle/crawler-detect
composer require matomo/device-detector
make doctrine-migration
```

## Upgrade to v1.4.0
### Commands

```
yarn remove node-sass
yarn add sass --dev --save
yarn add chart.js --save
composer require jaybizzle/crawler-detect
make doctrine-migration
make asset
```

### Configuration

```
// config/services.yaml
services:
    App\Core\EventListener\RedirectListener:
        tags:
            - { name: kernel.event_listener, event: kernel.exception }

    App\Core\EventListener\AnalyticListener:
        tags:
            - { name: kernel.event_listener, event: kernel.request }
```
