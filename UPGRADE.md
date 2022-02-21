## [Unreleased]

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
