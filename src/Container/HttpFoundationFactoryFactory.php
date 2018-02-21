<?php

declare(strict_types=1);

namespace WShafer\Expressive\Symfony\Router\Container;

use Psr\Container\ContainerInterface;
use Symfony\Bridge\PsrHttpMessage\Factory\HttpFoundationFactory;

class HttpFoundationFactoryFactory
{
    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function __invoke(ContainerInterface $container) : HttpFoundationFactory
    {
        return new HttpFoundationFactory();
    }
}
