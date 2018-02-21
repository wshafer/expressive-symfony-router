<?php

declare(strict_types=1);

namespace WShafer\Expressive\Symfony\Router\Container;

use Psr\Container\ContainerInterface;
use Symfony\Component\Routing\Generator\UrlGenerator;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\RouteCollection;

class UrlGeneratorFactory
{
    public function __invoke(ContainerInterface $container) : UrlGenerator
    {
        /** @var RouteCollection $routeCollection */
        $routeCollection = $container->get(RouteCollection::class);

        /** @var RequestContext $context */
        $context = $container->get(RequestContext::class);

        return new UrlGenerator($routeCollection, $context);
    }
}
