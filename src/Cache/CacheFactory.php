<?php

declare(strict_types=1);

namespace WShafer\Expressive\Symfony\Router\Cache;

use Psr\Container\ContainerInterface;

class CacheFactory
{
    public function __invoke(ContainerInterface $container) : Cache
    {
        $config = $container->has('config')
            ? $container->get('config')
            : [];

        $config = $config['router']['symfony'] ?? [];

        return new Cache($config);
    }
}
