<?php

namespace WShafer\Expressive\Symfony\Router\Test\Cache;

use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use WShafer\Expressive\Symfony\Router\Cache\Cache;
use WShafer\Expressive\Symfony\Router\Cache\CacheFactory;

class CacheFactoryTest extends TestCase
{
    public function testInvoke()
    {
        $container = $this->createMock(ContainerInterface::class);

        $container->expects($this->once())
            ->method('has')
            ->willReturn(true);

        $container->expects($this->once())
            ->method('get')
            ->willReturn([]);

        $factory = new CacheFactory();

        $instance = $factory($container);
        $this->assertInstanceOf(Cache::class, $instance);
    }
}
