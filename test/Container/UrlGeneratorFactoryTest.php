<?php

namespace WShafer\Expressive\Symfony\Router\Test\Container;

use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use Symfony\Component\Routing\Generator\UrlGenerator;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\RouteCollection;
use WShafer\Expressive\Symfony\Router\Container\UrlGeneratorFactory;

class UrlGeneratorFactoryTest extends TestCase
{
    public function testInvoke()
    {
        $container = $this->createMock(ContainerInterface::class);
        $mockRouteCollection = $this->getMockBuilder(RouteCollection::class)
            ->disableOriginalConstructor()
            ->getMock();
        $mockRequestContext = $this->getMockBuilder(RequestContext::class)
            ->disableOriginalConstructor()
            ->getMock();

        $map = [
            [RequestContext::class, $mockRequestContext],
            [RouteCollection::class, $mockRouteCollection]
        ];

        $container->expects($this->exactly(2))
            ->method('get')
            ->will($this->returnValueMap($map));

        $factory = new UrlGeneratorFactory();

        $instance = $factory($container);
        $this->assertInstanceOf(UrlGenerator::class, $instance);
    }
}
