<?php

declare(strict_types=1);

namespace WShafer\Expressive\Symfony\Router\Container;

use Psr\Container\ContainerInterface;
use Symfony\Component\Routing\RouteCollection;

class RouteCollectionFactory
{
    public function __invoke(ContainerInterface $container) : RouteCollection
    {
        return new RouteCollection();
    }
}
