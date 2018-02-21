<?php

declare(strict_types=1);

namespace WShafer\Expressive\Symfony\Router\Container;

use Psr\Container\ContainerInterface;
use Symfony\Bridge\PsrHttpMessage\Factory\HttpFoundationFactory;

class HttpFoundationFactoryFactory
{
    public function __invoke(ContainerInterface $container) : HttpFoundationFactory
    {
        return new HttpFoundationFactory();
    }
}
