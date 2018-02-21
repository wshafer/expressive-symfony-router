<?php

declare(strict_types=1);

namespace WShafer\Expressive\Symfony\Router;

use Psr\Container\ContainerInterface;
use Symfony\Component\Routing\Generator\UrlGenerator;
use Symfony\Component\Routing\Matcher\UrlMatcher;
use Symfony\Component\Routing\RouteCollection;

class SymfonyRouteRouterFactory
{
    public function __invoke(ContainerInterface $container) : SymfonyRouteRouter
    {
        $urlMatcher = $container->get(UrlMatcher::class);

        $urlGenerator = $container->get(UrlGenerator::class);

        $routes = $container->get(RouteCollection::class);

        return new SymfonyRouteRouter($routes, $urlMatcher, $urlGenerator);
    }
}
