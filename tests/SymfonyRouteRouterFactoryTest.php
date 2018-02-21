<?php

namespace WShafer\Expressive\Symfony\Router\Test;

use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use Symfony\Component\Routing\Generator\UrlGenerator;
use Symfony\Component\Routing\Matcher\UrlMatcher;
use Symfony\Component\Routing\RouteCollection;
use WShafer\Expressive\Symfony\Router\SymfonyRouteRouter;
use WShafer\Expressive\Symfony\Router\SymfonyRouteRouterFactory;

class SymfonyRouteRouterFactoryTest extends TestCase
{
    /**
     * @covers \WShafer\Expressive\Symfony\Router\SymfonyRouteRouterFactory
     */
    public function testInvoke()
    {
        $container = $this->createMock(ContainerInterface::class);
        $mockRouteCollection = $this->getMockBuilder(RouteCollection::class)
            ->disableOriginalConstructor()
            ->getMock();
        $mockUrlMatcher = $this->getMockBuilder(UrlMatcher::class)
            ->disableOriginalConstructor()
            ->getMock();
        $mockUrlGenerator = $this->getMockBuilder(UrlGenerator::class)
            ->disableOriginalConstructor()
            ->getMock();

        $map = [
            [RouteCollection::class, $mockRouteCollection],
            [UrlMatcher::class, $mockUrlMatcher],
            [UrlGenerator::class, $mockUrlGenerator]
        ];

        $container->expects($this->exactly(3))
            ->method('get')
            ->will($this->returnValueMap($map));

        $factory = new SymfonyRouteRouterFactory();

        $instance = $factory($container);
        $this->assertInstanceOf(SymfonyRouteRouter::class, $instance);
    }
}
