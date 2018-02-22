<?php

namespace WShafer\Expressive\Symfony\Router\Test;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\UriInterface;
use Symfony\Component\Routing\Exception\MethodNotAllowedException;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Symfony\Component\Routing\Generator\UrlGenerator;
use Symfony\Component\Routing\Matcher\UrlMatcher;
use Symfony\Component\Routing\RouteCollection;
use WShafer\Expressive\Symfony\Router\Cache\Cache;
use WShafer\Expressive\Symfony\Router\SymfonyRouteRouter;
use Zend\Expressive\Router\Route;
use Zend\Expressive\Router\RouteResult;

class SymfonyRouteRouterTest extends TestCase
{
    /** @var MockObject|RouteCollection */
    protected $mockRouteCollection;

    /** @var MockObject|UrlMatcher */
    protected $mockUrlMatcher;

    /** @var MockObject|UrlGenerator */
    protected $mockUrlGenerator;

    /** @var MockObject|Cache */
    protected $mockCache;

    /** @var SymfonyRouteRouter */
    protected $router;

    public function setup()
    {
        $this->mockRouteCollection = $this->getMockBuilder(RouteCollection::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->mockUrlMatcher = $this->getMockBuilder(UrlMatcher::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->mockUrlGenerator = $this->getMockBuilder(UrlGenerator::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->mockCache = $this->getMockBuilder(Cache::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->mockCache->expects($this->once())
            ->method('populateCollectionFromCache')
            ->with($this->equalTo($this->mockRouteCollection));

        $this->router = new SymfonyRouteRouter(
            $this->mockRouteCollection,
            $this->mockUrlMatcher,
            $this->mockUrlGenerator,
            $this->mockCache
        );

        $this->assertInstanceOf(SymfonyRouteRouter::class, $this->router);
    }

    public function testConstructor()
    {
    }

    public function testAddRoute()
    {
        /** @var MockObject|Route $mockRoute */
        $mockRoute = $this->createMock(Route::class);

        $mockRoute->expects($this->once())
            ->method('getPath')
            ->willReturn('/');

        $mockRoute->expects($this->once())
            ->method('getName')
            ->willReturn('home');

        $mockRoute->expects($this->once())
            ->method('getOptions')
            ->willReturn([]);

        $mockRoute->expects($this->once())
            ->method('getAllowedMethods')
            ->willReturn(null);

        $this->mockRouteCollection->expects($this->once())
            ->method('add')
            ->with(
                $this->equalTo('home'),
                $this->isInstanceOf(\Symfony\Component\Routing\Route::class)
            );

        $this->mockCache->expects($this->once())
            ->method('has')
            ->with($this->equalTo('home'))
            ->willReturn(false);

        $this->mockCache->expects($this->once())
            ->method('add')
            ->with(
                $this->equalTo('home'),
                $this->isInstanceOf(\Symfony\Component\Routing\Route::class)
            )->willReturn(true);

        $this->router->addRoute($mockRoute);
    }

    public function testMatch()
    {
        /** @var MockObject|Route $mockRoute */
        $mockRoute = $this->createMock(Route::class);

        $mockRequest = $this->createMock(ServerRequestInterface::class);
        $mockUri = $this->createMock(UriInterface::class);

        $mockRequest->expects($this->once())
            ->method('getUri')
            ->willReturn($mockUri);

        $mockUri->expects($this->once())
            ->method('getPath')
            ->willReturn('/');

        $this->mockUrlMatcher->expects($this->once())
            ->method('match')
            ->willReturn([
                'route' => 'home',
                '_route' => 'home'
            ]);

        $this->mockCache->expects($this->once())
            ->method('writeCache')
            ->willReturn(true);

        $this->router->addRoute($mockRoute);
        $return = $this->router->match($mockRequest);

        $this->assertInstanceOf(RouteResult::class, $return);
        $this->assertTrue($return->isSuccess());
        $this->assertEquals($mockRoute, $return->getMatchedRoute());
    }

    public function testMatchMethodNotAllowed()
    {
        $mockRequest = $this->createMock(ServerRequestInterface::class);
        $mockUri = $this->createMock(UriInterface::class);

        $mockRequest->expects($this->once())
            ->method('getUri')
            ->willReturn($mockUri);

        $mockUri->expects($this->once())
            ->method('getPath')
            ->willReturn('/');

        $this->mockUrlMatcher->expects($this->once())
            ->method('match')
            ->willThrowException(new MethodNotAllowedException(['GET']));

        $this->mockCache->expects($this->once())
            ->method('writeCache')
            ->willReturn(true);

        $return = $this->router->match($mockRequest);

        $this->assertInstanceOf(RouteResult::class, $return);
        $this->assertFalse($return->isSuccess());
    }

    public function testMatchResourceNotFound()
    {
        $mockRequest = $this->createMock(ServerRequestInterface::class);
        $mockUri = $this->createMock(UriInterface::class);

        $mockRequest->expects($this->once())
            ->method('getUri')
            ->willReturn($mockUri);

        $mockUri->expects($this->once())
            ->method('getPath')
            ->willReturn('/');

        $this->mockUrlMatcher->expects($this->once())
            ->method('match')
            ->willThrowException(new ResourceNotFoundException());

        $this->mockCache->expects($this->once())
            ->method('writeCache')
            ->willReturn(true);

        $return = $this->router->match($mockRequest);

        $this->assertInstanceOf(RouteResult::class, $return);
        $this->assertFalse($return->isSuccess());
    }

    public function testGenerate()
    {
        $this->mockUrlGenerator->expects($this->once())
            ->method('generate')
            ->with(
                $this->equalTo('home'),
                $this->equalTo([]),
                $this->equalTo(1)
            )
            ->willReturn('/');

        $return = $this->router->generateUri('home');
        $this->assertEquals('/', $return);
    }
}
