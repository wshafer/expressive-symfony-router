<?php

declare(strict_types=1);

namespace WShafer\Expressive\Symfony\Router;

use Symfony\Bridge\PsrHttpMessage\Factory\HttpFoundationFactory;
use Symfony\Component\Routing\Generator\UrlGenerator;
use Symfony\Component\Routing\Matcher\UrlMatcher;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\RouteCollection;
use WShafer\Expressive\Symfony\Router\Cache\Cache;
use WShafer\Expressive\Symfony\Router\Cache\CacheFactory;
use WShafer\Expressive\Symfony\Router\Container\HttpFoundationFactoryFactory;
use WShafer\Expressive\Symfony\Router\Container\RequestContextFactory;
use WShafer\Expressive\Symfony\Router\Container\RouteCollectionFactory;
use WShafer\Expressive\Symfony\Router\Container\UrlGeneratorFactory;
use WShafer\Expressive\Symfony\Router\Container\UrlMatcherFactory;
use Zend\Expressive\Router\RouterInterface;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class ConfigProvider
{
    public function __invoke() : array
    {
        return [
            'dependencies' => $this->getDependencies(),
        ];
    }

    public function getDependencies() : array
    {
        return [
            'aliases' => [
                RouterInterface::class => SymfonyRouteRouter::class,
            ],
            'factories' => [
                SymfonyRouteRouter::class => SymfonyRouteRouterFactory::class,
                HttpFoundationFactory::class => HttpFoundationFactoryFactory::class,
                RequestContext::class => RequestContextFactory::class,
                UrlMatcher::class => UrlMatcherFactory::class,
                UrlGenerator::class => UrlGeneratorFactory::class,
                RouteCollection::class => RouteCollectionFactory::class,
                Cache::class => CacheFactory::class
            ],
        ];
    }
}
