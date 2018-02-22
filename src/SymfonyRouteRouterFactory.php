<?php

declare(strict_types=1);

namespace WShafer\Expressive\Symfony\Router;

use Psr\Container\ContainerInterface;
use Symfony\Component\Routing\Generator\UrlGenerator;
use Symfony\Component\Routing\Matcher\UrlMatcher;
use Symfony\Component\Routing\RouteCollection;
use WShafer\Expressive\Symfony\Router\Cache\Cache;

class SymfonyRouteRouterFactory
{
    public function __invoke(ContainerInterface $container) : SymfonyRouteRouter
    {
        $urlMatcher = $container->get(UrlMatcher::class);

        $urlGenerator = $container->get(UrlGenerator::class);

        $routes = $container->get(RouteCollection::class);

        $cache = $container->get(Cache::class);

        return new SymfonyRouteRouter($routes, $urlMatcher, $urlGenerator, $cache);
    }
}
