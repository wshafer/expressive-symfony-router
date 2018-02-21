# Symfony Route Integration for Expressive

Provides [Symfony Route](https://symfony.com/doc/current/routing.html) integration for
[Expressive](https://github.com/zendframework/zend-expressive).

## Installation

Install this library using composer:

```bash
$ composer require wshafer/expressive-symfony-router
```

## Documentation

### Configuration

To enable this router using the Expressive Skeleton, make sure to add
`WShafer\Expressive\Symfony\Router\ConfigProvider::class` to your `ConfigAggregator`
located in `config/config.php`.  In addition you'll want to remove
your current router's config provider that was installed during setup.


### Routing
$app->route('/book/{id}', YourRequestHandler::class)