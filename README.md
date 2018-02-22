
# Symfony Route Integration for Expressive

[![Build Status](https://travis-ci.org/wshafer/expressive-symfony-router.svg?branch=master)](https://travis-ci.org/wshafer/expressive-symfony-router)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/wshafer/expressive-symfony-router/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/wshafer/expressive-symfony-router/?branch=master)
[![codecov](https://codecov.io/gh/wshafer/expressive-symfony-router/branch/master/graph/badge.svg)](https://codecov.io/gh/wshafer/expressive-symfony-router)

Provides [Symfony Route](https://symfony.com/doc/current/routing.html) integration for
[Expressive](https://github.com/zendframework/zend-expressive).

## Installation

Install this library using composer:

```bash
$ composer require symfony/routing:dev-master
$ composer require wshafer/expressive-symfony-router:dev-master
```

## Documentation

### Configuration

To enable this router using the Expressive Skeleton, make sure to add
`WShafer\Expressive\Symfony\Router\ConfigProvider::class` to your `ConfigAggregator`
located in `config/config.php`.  In addition you'll want to remove
your current router's config provider that was installed during setup.


### Routing
$app->route('/book/{id}', YourRequestHandler::class)


### Caching
To enable caching you need to add the following
config:

```php

return [
    'router' => [
        'symfony' => [
            'cache_enabled' => true,
            'cache_file'    => /my/cache/dir/cache_file.txt
        ],
    ],
];
```
