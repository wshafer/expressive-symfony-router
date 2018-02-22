<?php

declare(strict_types=1);

namespace WShafer\Expressive\Symfony\Router;

use Psr\Http\Message\ServerRequestInterface as Request;
use Symfony\Component\Routing\Exception\MethodNotAllowedException;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Symfony\Component\Routing\Generator\UrlGenerator;
use Symfony\Component\Routing\Matcher\UrlMatcher;
use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Routing\Route as SymfonyRoute;
use WShafer\Expressive\Symfony\Router\Cache\Cache;
use Zend\Expressive\Router\Route;
use Zend\Expressive\Router\RouteResult;
use Zend\Expressive\Router\RouterInterface;

/**
 * @SuppressWarnings(PHPMD.LongVariable)
 */
class SymfonyRouteRouter implements RouterInterface
{
    /** @var RouteCollection  */
    protected $collection;

    /** @var UrlMatcher */
    protected $urlMatcher;

    /** @var UrlGenerator */
    protected $generator;

    protected $cache;

    protected $routes = [];

    public function __construct(
        RouteCollection $collection,
        UrlMatcher $urlMatcher,
        UrlGenerator $generator,
        Cache $cache
    ) {
        $this->collection = $collection;
        $this->urlMatcher = $urlMatcher;
        $this->generator = $generator;
        $this->cache = $cache;

        $this->cache->populateCollectionFromCache($collection);
    }

    public function addRoute(Route $route): void
    {
        $path = $route->getPath();
        $name = $route->getName();

        $this->routes[$name] = $route;

        if ($this->cache->has($name)) {
            return;
        }

        $symfonyRoute = new SymfonyRoute(
            $path,
            ['route' => $name],
            [],
            $route->getOptions(),
            null,
            [],
            $route->getAllowedMethods(),
            null
        );

        $this->cache->add($name, $symfonyRoute);
        $this->collection->add($name, $symfonyRoute);
    }

    /**
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public function match(Request $request): RouteResult
    {
        $this->cache->writeCache();

        try {
            $match = $this->urlMatcher->match($request->getUri()->getPath());
        } catch (MethodNotAllowedException $e) {
            return RouteResult::fromRouteFailure($e->getAllowedMethods());
        } catch (ResourceNotFoundException $e) {
            return RouteResult::fromRouteFailure(null);
        }

        $route = $match['route'];

        if (!$this->routes[$route]) {
            $this->cache->invalidateCacheFile();
            return RouteResult::fromRouteFailure(null);
        }

        unset($match['route'], $match['_route']);
        return RouteResult::fromRoute($this->routes[$route], $match);
    }

    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function generateUri(string $name, array $substitutions = [], array $options = []): string
    {
        return $this->generator->generate($name, $substitutions);
    }
}
